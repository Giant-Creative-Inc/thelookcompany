# Salient Video Library

## Category ordering (1.0.3)

Edit **Video Library (Grouped)** in WPBakery. Under **Category order**, select category names, then drag the selected items into the desired sequence and save the element/page.

Selected categories appear first. Unselected and newly added categories follow alphabetically. Leave the list empty to keep the original alphabetical order. Removing a category from the list does not hide it.

Ordering applies to category sections only; videos remain newest first. Active filters still determine which categories appear, and **Max categories** is applied after ordering. Category archives remain locked to their current category.

Each element retains its own ordering and display settings, including during AJAX filtering and clearing filters.

For direct shortcode use, supply comma-separated video-category term IDs:

```text
[svl_video_library category_order="12,8,23" per_category="3"]
```

Duplicate, invalid, deleted, or ineligible IDs are ignored. Existing shortcodes need no changes.
