<?php
$product_id = get_query_var('product_id');
$product = wc_get_product($product_id);
$face = get_post_meta($product_id, 'face', true) ?: 'lips';
$token = get_option('virtual_makeup_token');
$color_attribute_key = get_option('virtual_makeup_color_attribute', 'pa_color'); 

// Get colors from product attributes
$colors = array();
$counter = 1;
$attributes = $product->get_attributes();
// Define pattern types for lips
$patterns = array(
    'normal' => 'معمولی',
    'matte' => 'مات',
    'glossy' => 'براق',
    'glitter' => 'اکلیلی'
);

// Use the selected attribute key
if (!empty($color_attribute_key) && isset($attributes[$color_attribute_key])) {
    $color_terms = get_the_terms($product_id, $color_attribute_key); // Use the dynamic key
    if ($color_terms && !is_wp_error($color_terms)) {
        foreach ($color_terms as $term) {
            // Ensure you have a 'color' meta field for your attribute terms
            $color_hex = get_term_meta($term->term_id, 'color', true);
            if (!empty($color_hex)) {
                $colors[] = array(
                    'code' => $term->name,
                    'color' => $color_hex
                );
            }
        }
    }
}

if (empty($colors)) {
    // Keep default colors or handle the case where no colors are found
    $colors = array(
        array('code' => '01', 'color' => '#000000'),
        array('code' => '02', 'color' => '#4B0082')
    );
}

$drop_svg_outline = '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-drop-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.07 15.34c1.115 .88 2.74 .88 3.855 0c1.115 -.88 1.398 -2.388 .671 -3.575l-2.596 -3.765l-2.602 3.765c-.726 1.187 -.443 2.694 .672 3.575z" /><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /></svg>';
$drop_svg_filed   = '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="currentColor"  class="icon icon-tabler icons-tabler-filled icon-tabler-drop-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 3.34a10 10 0 1 1 -15 8.66l.005 -.324a10 10 0 0 1 14.995 -8.336m-4.177 4.092a1 1 0 0 0 -1.646 0l-2.602 3.764c-1.022 1.67 -.634 3.736 .875 4.929a4.144 4.144 0 0 0 5.095 0c1.51 -1.191 1.897 -3.26 .904 -4.882z" /></svg>';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo esc_html($product->get_title()); ?> - Virtual Makeup</title>
    <link rel="stylesheet" href="https://sdk.armogroup.tech/makeup/v2/makeup.min.css">
    <style>
        html{margin:0;padding:0;}
        body { margin: 0; padding: 0;  }
        #virtual-mackup { padding: 0; }
        #armo-makeup-view { min-height: 300px; position: relative; }


        /* Add new slider styles */
        .transparency-control {
            position: absolute;
            right: 10px;
            top: 20px;
            /*transform: translateY(-50%);*/
            width: 30px;
            /*height: 150px;*/
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .vertical-slider {
            width: 6px;
            height: 100%;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 1) 0%, rgba(255, 255, 255, 0) 100%);;
            border-radius: 3px;
            position: relative;
            cursor: pointer;
            box-shadow: 0 0 8px 0px rgba(0,0,0,0.1);
        }

        .vertical-slider-handle {
            width: 16px;
            height: 16px;
            background: #fff;
            box-shadow: 0 0 3px 3px rgba(0,0,0,0.2);
            border-radius: 50%;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            cursor: pointer;
        }

        .transparency-value {
            margin-top: 10px;
            text-align: center;
            font-size: 12px;
            display: none;
        }
        .vertical-slider-wrapper{
            display: flex;
            flex-direction: column;
            gap: 2px;
            align-items: center;
            justify-content: space-between;
            height: 200px
        }
        .vertical-slider-wrapper .icon-full,
        .vertical-slider-wrapper .icon-zero{
            display: flex;
            width: 24px;
            height: 24px;
            align-items: center;
            justify-content: center;
            display: none;
        }
        .vertical-slider-wrapper svg{
            color: #fff;
        }

        #pattern-controls{
            display: none;
            position: absolute;
            top: 20px;
            width: auto;
            right: 50%;
            transform: translateX(50%);
            flex-direction: row;
            gap: 4px;
            justify-content: space-around;
        }
        #pattern-controls button{
            background: rgba(0,0,0,0.3);
            color: #fff;
            padding: 2px 6px;
            border-radius: 3px;
        }
        #pattern-controls button.active{
            background: #000;
            color: #fff;
        }
        .armo-sdk-color{
            border-radius: 5px;
        }
        .armo-sdk-color-code {
            font-size: 9px;
            line-height: 1em;
            white-space: break-spaces;
            color: #fff;
            text-shadow: 0px 0 3px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
<div id="virtual-mackup">
    <div id="armo-makeup-view"></div>

    <?php if($face === 'lips'):?>
    <div id="pattern-controls" class="control-group pattern-controls" style="display:none;">
        <?php
        // Create buttons for pattern types
        foreach ($patterns as $type => $label) {
            $active_class = ($type === 'normal') ? ' active' : '';
            echo '<button class="pattern-btn' . $active_class . '" onclick="setPattern(\'' . esc_attr($type) . '\')" data-pattern="' . esc_attr($type) . '">' . esc_html($label) . '</button>';
        }
        ?>
    </div>
    <?php endif; ?>

    <!-- Replace the existing transparency control with this new markup -->
    <div id="transparency-control" class="transparency-control" style="display: none">
        <div class="vertical-slider-wrapper">
            <span class="icon-full"><?php echo $drop_svg_filed;?></span>
            <div class="vertical-slider" id="vertical-slider">
                <div class="vertical-slider-handle" id="vertical-slider-handle"></div>
            </div>
            <span class="icon-zero"><?php echo $drop_svg_outline;?></span>
        </div>
        <div class="transparency-value" id="transparency-value">50%</div>
    </div>

    <div id="loading">دریافت اطلاعات...</div>
    <div id="error-message"></div>
</div>

    <script src="https://sdk.armogroup.tech/makeup/v2/makeup.js"></script>
    <script>
        let makeupApp;
        let currentFaceType = "<?php echo esc_js($face); ?>";


        document.addEventListener("DOMContentLoaded", () => {
            // تنظیم توکن
            window.Makeup.token = "<?php echo esc_js($token); ?>";

            // ایجاد نمونه از SDK با رنگ‌های محصول و پارامتر face
            makeupApp = new window.Makeup({
                colors: <?php echo json_encode($colors); ?>, // Pass the dynamically fetched colors
                pattern: "normal",
                face: currentFaceType,
                onReady: handleReady,
                onError: handleError
            });


            // Check if face type is lips to show pattern controls
            updateControlsVisibility();
        });

        function handleReady() {
            console.log(makeupApp.getStatus());
            document.getElementById("loading").style.display = "none";
            document.getElementById("transparency-control").style.display = "block";

            // Update active button
            updateActiveButton();
            initVerticalSlider();
            updateControlsVisibility()
        }

        function handleError(error) {
            console.error("SDK Error:", error);
            document.getElementById("error-message").textContent = error.message;
        }

        function changeType(type) {
            if (makeupApp.isFeatureEnabled(type)) {
                makeupApp.changeMakeupType(type);
                currentFaceType = type;

                // Update active button
                updateActiveButton();

                // Update controls visibility based on selected type
                updateControlsVisibility();

            } else {
                alert("این ویژگی در دسترس نیست");
            }
        }

        function setPattern(pattern) {
            makeupApp.setMakeupPattern(currentFaceType, pattern);

            // Update active pattern button
            const patternButtons = document.querySelectorAll(".pattern-btn");
            patternButtons.forEach(btn => {
                btn.classList.remove("active");
                if (btn.getAttribute("data-pattern") === pattern) {
                    btn.classList.add("active");
                }
            });
        }

        function updateActiveButton() {
            // Update face type buttons
            const faceButtons = document.querySelectorAll(".face-btn");
            faceButtons.forEach(btn => {
                btn.classList.remove("active");
                if (btn.getAttribute("data-face") === currentFaceType) {
                    btn.classList.add("active");
                }
            });

            // If lips is active, activate the normal pattern button by default
            if (currentFaceType === "lips") {
                const normalPatternBtn = document.querySelector('[data-pattern="normal"]');
                if (normalPatternBtn) {
                    normalPatternBtn.classList.add("active");
                }
            }
        }

        function updateControlsVisibility() {
            const patternControls = document.getElementById("pattern-controls");
            if (patternControls) {
                patternControls.style.display = currentFaceType === "lips" ? "flex" : "none";
            }
        }


        function initVerticalSlider() {
            const slider = document.querySelector('.vertical-slider');
            const handle = document.querySelector('.vertical-slider-handle');
            const value = document.querySelector('.transparency-value');
            let isDragging = false;

            function updateSlider(clientY) {
                const rect = slider.getBoundingClientRect();
                let y = clientY - rect.top;
                y = Math.max(0, Math.min(y, rect.height));

                const percentage = 1 - (y / rect.height);
                handle.style.top = `${y}px`;
                value.textContent = `${Math.round(percentage * 100)}%`;

                if (makeupApp) {
                    makeupApp.setMakeupTransparency(currentFaceType, percentage);
                }
            }

            // Mouse Events
            handle.addEventListener('mousedown', () => {
                isDragging = true;
            });

            document.addEventListener('mousemove', (e) => {
                if (isDragging) {
                    updateSlider(e.clientY);
                    e.preventDefault();
                }
            });

            document.addEventListener('mouseup', () => {
                isDragging = false;
            });

            slider.addEventListener('click', (e) => {
                updateSlider(e.clientY);
            });

            // Touch Events
            handle.addEventListener('touchstart', (e) => {
                isDragging = true;
                e.preventDefault(); // Prevent scrolling when touching the handle
            });

            document.addEventListener('touchmove', (e) => {
                if (isDragging && e.touches && e.touches[0]) {
                    updateSlider(e.touches[0].clientY);
                    e.preventDefault();
                }
            });

            document.addEventListener('touchend', () => {
                isDragging = false;
            });

            slider.addEventListener('touchstart', (e) => {
                if (e.touches && e.touches[0]) {
                    updateSlider(e.touches[0].clientY);
                }
                e.preventDefault();
            });

            // Set initial position
            handle.style.top = '50';
        }
</script>

</body>
</html>