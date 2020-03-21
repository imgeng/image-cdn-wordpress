class ImageCDNValidationError extends Error {
    constructor(side, message) {
        super(message)
        this.name = 'ImageCDNValidationError'
        this.side = side
    }
}

async function imageCDNCheckURLs(local_url, cdn_url) {

    const title_matcher = new RegExp('< *title *>(.+?)</ *title *>', 'si')
    const local_url_obj = new URL(local_url)
    const cdn_url_obj = new URL(cdn_url)
    
    if (local_url_obj.protocol === 'https' && cdn_url_obj.protocol !== 'https') {
        throw new ImageCDNValidationError('local', 'cannot test HTTP CDN URL from HTTPS')
    }

    const options = {
        cache: 'no-cache',
        referrerPolicy: 'no-referrer',
        mode: 'no-cors',
    }

    // Local site
    let local_res = {}
    let local_body = ''
    try {
        local_res = await fetch(local_url, options)
        console.log(local_res)
        local_body = await local_res.text()
    } catch (err) {
        throw new ImageCDNValidationError('local', err.message)
    }

    if (!local_res.ok) {
        throw new ImageCDNValidationError('local', `server returned HTTP ${local_res.status}`)
    }

    let local_title = ''
    let m = local_body.match(title_matcher)
    if (m) {
        local_title = m[1]
    }

    // CDN site
    let cdn_res = {}
    let cdn_body = ''
    try {
        cdn_res = await fetch(cdn_url, options)
        console.log(cdn_res)
        cdn_body = await cdn_res.text()
    } catch (err) {
        throw new ImageCDNValidationError('cdn', err.message)
    }

    if (!cdn_res.ok) {
        if (cdn_res.status === 502) {
            const status = cdn_res.headers.get('x-origin-status')
            const reason = cdn_res.headers.get('x-origin-reason')
            if (status !== null) {
                throw new ImageCDNValidationError('cdn', `origin server returned HTTP ${status}: ${reason}`)
            }
        }
        throw new ImageCDNValidationError('cdn', `server returned HTTP ${cdn_res.status}`)
    }

    let cdn_title = ''
    let m2 = cdn_body.match(title_matcher)
    if (m2) {
        cdn_title = m2[1]
    }

    if (local_title !== cdn_title) {
        throw new ImageCDNValidationError('cdn', 'local and cdn contents do not match')
    }

}