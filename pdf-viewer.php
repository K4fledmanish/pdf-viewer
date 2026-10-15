<?php
/**
 * Plugin Name:       PDF Viewer
 * Plugin URI:        https://github.com/K4fledmanish
 * Description:       Secure PDF viewer with page navigation and protection.
 * Version:           1.8
 * Author:            Manish Kafle
 * Author URI:        https://k4fledmanish.github.io/
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function pdfv_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'url'   => '',
			'src'   => '',
			'width' => '100%',
			'proxy' => 'no', // 'yes' or 'no'.
		),
		$atts,
		'pdf_viewer'
	);

	$source = ! empty( $atts['src'] ) ? $atts['src'] : $atts['url'];

	if ( empty( $source ) ) {
		return '<p style="color:red;">Error: PDF URL is missing.</p>';
	}

	$pdf_url = esc_url_raw( $source );
	if ( empty( $pdf_url ) ) {
		return '<p style="color:red;">Error: Invalid PDF URL.</p>';
	}

	$id = 'pdf_' . wp_rand( 1000, 99999 );

	$final_url = ( 'yes' === $atts['proxy'] )
		? 'https://api.allorigins.win/raw?url=' . rawurlencode( $pdf_url )
		: $pdf_url;

	ob_start();
	?>
	<div class="pdf-viewer-container" style="max-width: <?php echo esc_attr( $atts['width'] ); ?>; margin: 20px auto;">

		<div id="container-<?php echo esc_attr( $id ); ?>" style="background:white; padding:10px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
			<canvas id="canvas-<?php echo esc_attr( $id ); ?>" style="display:block; margin:0 auto; max-width:100%;"></canvas>
		</div>

		<div id="controls-<?php echo esc_attr( $id ); ?>" style="background:#333; color:white; padding:12px; text-align:center; border-radius:4px; margin-top:10px;">
			<button type="button" id="prev-<?php echo esc_attr( $id ); ?>" style="padding:8px 16px; margin:0 5px;">Previous</button>
			<span style="margin:0 15px; font-size:18px;">
				Page <span id="page_num-<?php echo esc_attr( $id ); ?>">1</span> of <span id="page_count-<?php echo esc_attr( $id ); ?>">?</span>
			</span>
			<button type="button" id="next-<?php echo esc_attr( $id ); ?>" style="padding:8px 16px; margin:0 5px;">Next</button>
		</div>

	</div>

	<script type="module">
		import * as pdfjsLib from <?php echo wp_json_encode( plugins_url( 'assets/js/pdf.min.js', __FILE__ ) ); ?>;

		pdfjsLib.GlobalWorkerOptions.workerSrc = <?php echo wp_json_encode( plugins_url( 'assets/js/pdf.worker.min.js', __FILE__ ) ); ?>;

		const id = <?php echo wp_json_encode( $id ); ?>;
		const url = <?php echo wp_json_encode( $final_url ); ?>;
		let pdfDoc = null, pageNum = 1, pageRendering = false, pageNumPending = null;
		const canvas = document.getElementById('canvas-' + id);
		const ctx = canvas.getContext('2d');

		document.addEventListener('contextmenu', e => e.preventDefault());

		document.addEventListener('keydown', function (e) {
			if (e.ctrlKey && ['p', 's'].includes(e.key.toLowerCase())) {
				e.preventDefault();
				alert("Printing and Saving are disabled.");
				return false;
			}
		});

		function renderPage(num) {
			pageRendering = true;
			pdfDoc.getPage(num).then(page => {
				const container = document.getElementById('container-' + id);
				let viewport = page.getViewport({ scale: 1 });
				const scale = (container.clientWidth * 0.96) / viewport.width;
				viewport = page.getViewport({ scale: scale });

				canvas.height = viewport.height;
				canvas.width = viewport.width;

				page.render({ canvasContext: ctx, viewport: viewport }).promise.then(() => {
					pageRendering = false;
					if (pageNumPending !== null) {
						renderPage(pageNumPending);
						pageNumPending = null;
					}
				});
			});
			document.getElementById('page_num-' + id).textContent = num;
		}

		function queueRenderPage(num) {
			if (pageRendering) pageNumPending = num;
			else renderPage(num);
		}

		document.getElementById('prev-' + id).addEventListener('click', () => {
			if (pageNum <= 1) return;
			pageNum--;
			queueRenderPage(pageNum);
		});

		document.getElementById('next-' + id).addEventListener('click', () => {
			if (!pdfDoc || pageNum >= pdfDoc.numPages) return;
			pageNum++;
			queueRenderPage(pageNum);
		});

		pdfjsLib.getDocument(url).promise.then(pdf => {
			pdfDoc = pdf;
			document.getElementById('page_count-' + id).textContent = pdf.numPages;
			renderPage(pageNum);
		}).catch(err => {
			console.error(err);
			document.getElementById('container-' + id).innerHTML =
				'<p style="color:red;padding:40px;text-align:center;">Failed to load PDF.<br><br>Check the file URL and the browser console.</p>';
		});

		window.addEventListener('resize', () => {
			if (pdfDoc) renderPage(pageNum);
		});
	</script>
	<?php
	return ob_get_clean();
}
add_shortcode( 'pdf_viewer', 'pdfv_shortcode' );
