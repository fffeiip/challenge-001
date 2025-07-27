<?php
namespace Core;

class ViewHelper
{
    /**
     * Build a URL preserving filter and sort state for a given page.
     * @param array $meta  ['filter','sortBy','sortDir','page','perPage']
     * @param int   $page  Target page number
     * @return string       Query string starting with '?'
     */
    public static function pageUrl(array $meta, int $page): string
    {
        $qs = http_build_query([
            'filter' => $meta['filter'],
            'sort'   => $meta['sortBy'],
            'dir'    => $meta['sortDir'],
            'page'   => $page,
        ]);
        return '?' . $qs;
    }

    /**
     * Generate a sortable column link with arrow indicator.
     * @param array  $meta   ['filter','sortBy','sortDir','page']
     * @param string $column Column name
     * @param string $label  Display label
     * @return string         HTML <a> tag
     */
    public static function sortLink(array $meta, string $column, string $label): string
    {
        $dir = ($meta['sortBy'] === $column && $meta['sortDir'] === 'asc') ? 'desc' : 'asc';
        $url = http_build_query([
            'filter' => $meta['filter'],
            'sort'   => $column,
            'dir'    => $dir,
            'page'   => 1,
        ]);
        $arrow = '';

        return sprintf('<a href="?%s">%s%s</a>', $url, htmlspecialchars($label), $arrow);
    }

    /**
     * Render pagination controls HTML.
     * @param array $meta ['filter','sortBy','sortDir','page','totalPages','total']
     * @return string HTML block
     */
    public static function renderPagination(array $meta): string
    {
        $html = '<div class="pagination" style="display:flex;align-items:center;justify-content:center;gap:0.5rem;margin:1rem 0;">';
        // Prev
        $prevDisabled = $meta['page'] <= 1 ? 'disabled' : '';
        $html .= sprintf(
            '<button %s onclick="location.href=\'%s\'">&laquo; Prev</button>',
            $prevDisabled,
            self::pageUrl($meta, $meta['page'] - 1)
        );
        // Pages
        for ($i = 1; $i <= $meta['totalPages']; $i++) {
            $active = $i === $meta['page'] ? 'class="active"' : '';
            $html .= sprintf(
                '<button %s onclick="location.href=\'%s\'">%d</button>',
                $active,
                self::pageUrl($meta, $i),
                $i
            );
        }
        // Next
        $nextDisabled = $meta['page'] >= $meta['totalPages'] ? 'disabled' : '';
        $html .= sprintf(
            '<button %s onclick="location.href=\'%s\'">Next &raquo;</button>',
            $nextDisabled,
            self::pageUrl($meta, $meta['page'] + 1)
        );

        $html .= sprintf(
            '<span style="margin-left:1rem;font-size:0.9em;">Page %d of %d — %d total</span>',
            $meta['page'], $meta['totalPages'], $meta['total']
        );
        $html .= '</div>';
        return $html;
    }

    /**
     * Render a filter form with text input and optional dropdown filters.
     * @param array  $meta      ['filter','sortBy','sortDir','page']
     * @param string $action    Form action URL
     * @param array  $dropdowns ['fieldName' => ['value'=>'Label', ...]]
     */
    public static function renderFilterForm(array $meta, string $action = '', array $dropdowns = []): string
    {
        $actionAttr = $action ? ' action="'.htmlspecialchars($action).'"' : '';
        $html = '<div class="table-search"><form method="get" class="table-search"'.$actionAttr.' style="justify-content:flex-start;margin-bottom:1rem;">';

        // Text search
        $html .= '<input type="text" name="filter" value="'.htmlspecialchars($meta['filter']).'" placeholder="Search…" />';

        // Dropdown filters
        foreach ($dropdowns as $field => $options) {
            $selected = $_GET[$field] ?? '';
            $html .= '<select name="'.htmlspecialchars($field).'">';
            $html .= '<option value="">All '.htmlspecialchars(ucfirst($field)).'</option>';
            foreach ($options as $value => $label) {
                $sel = ((string)$selected === (string)$value) ? ' selected' : '';
                $html .= '<option value="'.htmlspecialchars($value).'"'.$sel.'>'.htmlspecialchars($label).'</option>';
            }
            $html .= '</select>';
        }

        // Preserve sort, dir, reset page
        $html .= '<input type="hidden" name="sort" value="'.htmlspecialchars($meta['sortBy']).'" />';
        $html .= '<input type="hidden" name="dir" value="'.htmlspecialchars($meta['sortDir']).'" />';
        $html .= '<input type="hidden" name="page" value="1" />';

        $html .= '<button type="submit">Filter</button>';
        $html .= '</form></div>';

        return $html;
    }
}
