<?php
/**
 * Output custom ACF JSON-LD schema before page/post content.
 * Hook: Salient Theme — Before Page/Post Content
 */
add_action('nectar_hook_before_content_global_section', function () {
    if (!function_exists('get_field')) {
        return;
    }

    $schema = get_field('custom_page_schema');

    if (empty($schema)) {
        return;
    }

    // if acf returns a string (textarea), decode and re-encode to ensure valid json.
    if (is_string($schema)) {
        $decoded = json_decode($schema);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return; // bail if the stored value isn't valid JSON.
        }

        $schema = $decoded;
    }

    printf(
        '<script type="application/ld+json">%s</script>'."\n",
        wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    );
});
