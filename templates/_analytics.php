<?php

$options = $options ?? [];

use ImageEngine\Settings;

try {
	$cname = $options['url'] ?? '';
	$cname = str_replace( 'https://', '', $cname );
	$cname = str_replace( '.imgeng.in', '', $cname );

	$statistics = Settings::client()->statistics( $cname );

	if(!is_array($statistics) || !isset($statistics['metadata']['period']['start']) || !isset($statistics['metadata']['period']['end'])) {
		return;
	}

	$start = date( "Y-m-d", strtotime( $statistics['metadata']['period']['start'] ) );
	$end   = date( "Y-m-d", strtotime( $statistics['metadata']['period']['end'] ) );
} catch ( \Exception $e ) {
	return;
}
?>

<style>
	#analytics {
		display: grid;
		grid-template-columns: 1fr 1fr;
		gap: 20px;

		@media (max-width: 768px) {
			grid-template-columns: 1fr;
		}

		max-width: 1080px;

		margin-bottom: 20px;

		.card {
			width: 100%;
			display: flex;
			flex-direction: column;

			.card-title {
				display: flex;
				flex-direction: row;
				justify-content: space-between;
				align-items: center;

				h3 {
					color: #8898aa;
					text-transform: uppercase;
				}

				i {

					width: 32px;
					height: 32px;
					border-radius: 50%;

					display: flex;
					justify-content: center;
					align-items: center;

					margin-right: -7px;

					&.bg-gradient-info {
						background: linear-gradient(87deg, #11cdef, #1162ef) !important;
					}

					&.bg-gradient-red {
						background: linear-gradient(87deg, #f5365c, #f56c36) !important;
					}

					&.bg-gradient-pink-bright {
						background: linear-gradient(225deg, #f6f, #4636b3) !important;
					}

					svg {
						width: 16px;
						height: 16px;
						fill: #fff;
					}
				}

			}

			.card-body {
				margin-bottom: auto;

				.b {
					font-size: 22px;
					font-weight: bold;
					color: #32325d;
					margin-bottom: 15px;
				}

				.i {
					font-size: 16px;
					color: #525f7f;
					margin-bottom: 15px;
				}
			}

			.card-footer {
				justify-self: flex-end;
				margin-top: 15px;
				font-size: 12px;
				color: #525f7f;
			}
		}
	}
</style>


<div class="card">
	<div class="card-title">
		<h3>Carbon emission saved</h3>
		<i class="bg-gradient-info">
			<svg height="512px" id="Layer_1" style="enable-background:new 0 0 512 512;" version="1.1"
				 viewBox="0 0 512 512" width="512px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
				 xmlns:xlink="http://www.w3.org/1999/xlink"><g>
					<path d="M368,224c26.5,0,48-21.5,48-48c0-26.5-21.5-48-48-48c-26.5,0-48,21.5-48,48C320,202.5,341.5,224,368,224z"/>
					<path d="M452,64H60c-15.6,0-28,12.7-28,28.3v327.4c0,15.6,12.4,28.3,28,28.3h392c15.6,0,28-12.7,28-28.3V92.3   C480,76.7,467.6,64,452,64z M348.9,261.7c-3-3.5-7.6-6.2-12.8-6.2c-5.1,0-8.7,2.4-12.8,5.7l-18.7,15.8c-3.9,2.8-7,4.7-11.5,4.7   c-4.3,0-8.2-1.6-11-4.1c-1-0.9-2.8-2.6-4.3-4.1L224,215.3c-4-4.6-10-7.5-16.7-7.5c-6.7,0-12.9,3.3-16.8,7.8L64,368.2V107.7   c1-6.8,6.3-11.7,13.1-11.7h357.7c6.9,0,12.5,5.1,12.9,12l0.3,260.4L348.9,261.7z"/>
				</g></svg>
		</i>
	</div>

	<div class="card-body">
		<div class="b"><?php echo !empty($statistics['carbon_emissions_saved']['saved']['nice']) ?
				$statistics['carbon_emissions_saved']['saved']['nice'] . __(' saved', 'image-cdn') : "0" ?></div>
		<div class="i">Original carbon emissions: <?php echo $statistics['carbon_emissions_saved']['original']['nice'] ?? "0" ?></div>
		<div class="i">Optimized carbon emissions: <?php echo $statistics['carbon_emissions_saved']['final']['nice'] ?? "0" ?></div>
	</div>

	<div class="card-footer">
		From <?php echo $start ?> to <?php echo $end ?>
	</div>
</div>

<div class="card">
	<div class="card-title">
		<h3>Image payload reduction</h3>
		<i class="bg-gradient-info">
			<svg height="512px" id="Layer_1" style="enable-background:new 0 0 512 512;" version="1.1"
				 viewBox="0 0 512 512" width="512px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
				 xmlns:xlink="http://www.w3.org/1999/xlink"><g>
					<path d="M368,224c26.5,0,48-21.5,48-48c0-26.5-21.5-48-48-48c-26.5,0-48,21.5-48,48C320,202.5,341.5,224,368,224z"/>
					<path d="M452,64H60c-15.6,0-28,12.7-28,28.3v327.4c0,15.6,12.4,28.3,28,28.3h392c15.6,0,28-12.7,28-28.3V92.3   C480,76.7,467.6,64,452,64z M348.9,261.7c-3-3.5-7.6-6.2-12.8-6.2c-5.1,0-8.7,2.4-12.8,5.7l-18.7,15.8c-3.9,2.8-7,4.7-11.5,4.7   c-4.3,0-8.2-1.6-11-4.1c-1-0.9-2.8-2.6-4.3-4.1L224,215.3c-4-4.6-10-7.5-16.7-7.5c-6.7,0-12.9,3.3-16.8,7.8L64,368.2V107.7   c1-6.8,6.3-11.7,13.1-11.7h357.7c6.9,0,12.5,5.1,12.9,12l0.3,260.4L348.9,261.7z"/>
				</g></svg>
		</i>
	</div>

	<div class="card-body">
		<div class="b"><?php echo !empty($statistics['payload_reduction']['percent']) ? number_format($statistics['payload_reduction']['percent'],1) : 0 ?> %</div>
		<div class="i">Original size: <?php echo $statistics['payload_reduction']['original'] ?></div>
		<div class="i">Optimized size: <?php echo $statistics['payload_reduction']['optimized'] ?></div>
	</div>

	<div class="card-footer">
		From <?php echo $start ?> to <?php echo $end ?>
	</div>
</div>

<div class="card">
	<div class="card-title">
		<h3>Smartbytes transfered</h3>
		<i class="bg-gradient-red">
			<svg height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg"><title/>
				<path d="M477.64,38.26a4.75,4.75,0,0,0-3.55-3.66c-58.57-14.32-193.9,36.71-267.22,110a317,317,0,0,0-35.63,42.1c-22.61-2-45.22-.33-64.49,8.07C52.38,218.7,36.55,281.14,32.14,308a9.64,9.64,0,0,0,10.55,11.2L130,309.57a194.1,194.1,0,0,0,1.19,19.7,19.53,19.53,0,0,0,5.7,12L170.7,375a19.59,19.59,0,0,0,12,5.7,193.53,193.53,0,0,0,19.59,1.19l-9.58,87.2a9.65,9.65,0,0,0,11.2,10.55c26.81-4.3,89.36-20.13,113.15-74.5,8.4-19.27,10.12-41.77,8.18-64.27a317.66,317.66,0,0,0,42.21-35.64C441,232.05,491.74,99.74,477.64,38.26ZM294.07,217.93a48,48,0,1,1,67.86,0A47.95,47.95,0,0,1,294.07,217.93Z"/>
				<path d="M168.4,399.43c-5.48,5.49-14.27,7.63-24.85,9.46-23.77,4.05-44.76-16.49-40.49-40.52,1.63-9.11,6.45-21.88,9.45-24.88a4.37,4.37,0,0,0-3.65-7.45,60,60,0,0,0-35.13,17.12C50.22,376.69,48,464,48,464s87.36-2.22,110.87-25.75A59.69,59.69,0,0,0,176,403.09C176.37,398.91,171.28,396.42,168.4,399.43Z"/>
			</svg>
		</i>
	</div>

	<div class="card-body">
		<div class="b"><?php echo $statistics['smart_bytes'] ?></div>
	</div>

	<div class="card-footer">
		From <?php echo $start ?> to <?php echo $end ?>
	</div>
</div>

<div class="card">
	<div class="card-title">
		<h3>Cache hit ratio</h3>
		<i class="bg-gradient-pink-bright">
			<svg id="Layer_1_1_" style="enable-background:new 0 0 16 16;" viewBox="0 0 16 16" xml:space="preserve"
				 xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path
						d="M8,16c4.079,0,7.438-3.055,7.931-7H7.778l-5.027,5.027C4.156,15.253,5.989,16,8,16z"/>
				<path d="M8,0v8h8C16,3.582,12.418,0,8,0z"/>
				<path d="M0,8c0,2.047,0.775,3.909,2.04,5.324L7,8.364V8V0.069C3.055,0.562,0,3.921,0,8z"/></svg>
		</i>
	</div>

	<div class="card-body">
		<div class="b"><?php echo !empty($statistics['cache_hit_ratio']['hit_percentage']) ?
				number_format($statistics['cache_hit_ratio']['hit_percentage'],1) : "0" ?> %</div>
		<div class="i">Hits: <?php echo $statistics['cache_hit_ratio_nice']['hits'] ?? "0" ?></div>
		<div class="i">Misses: <?php echo $statistics['cache_hit_ratio_nice']['misses'] ?? "0" ?></div>
	</div>

	<div class="card-footer">
		From <?php echo $start ?> to <?php echo $end ?>
	</div>
</div>