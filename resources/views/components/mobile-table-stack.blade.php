{{--
    Phone layout for wide tables.

    Below 768px, a table that is wider than its box would scroll sideways and hide
    most of its columns. This turns each such table into one card per row, using the
    shared .ds-table--stack styles in admin.css:
      - every cell gets data-label="<its column header>"
      - index columns ("#", "SL", "No") and photo columns are hidden
      - the "Name" column (or else the first other column) becomes the card title,
        shown first
      - an "Action(s)" column becomes the card's button row

    Left alone (they keep scrolling sideways):
      - tables that already fit, or already use .ds-table--stack
      - tables without a single-row <thead>, or with colspan/rowspan cells
      - tables with form fields (marks entry, opening stock …) or a <tfoot> (reports)
      - anything marked data-no-stack
--}}
<script>
(function () {
    var PHONE = window.matchMedia('(max-width: 767.98px)');
    var INDEX = /^(#|sl\.?|s\/l|no\.?|serial)$/i;
    var MEDIA = /^(photo|image|picture|avatar)$/i;
    var ACTIONS = /^actions?$/i;
    var NAME = /\bname\b/i;
    var OTHER_LANGUAGE = /bangla|\(bn\)|\bbn\b/i;

    function headers(table) {
        var head = table.tHead;
        if (!head || head.rows.length !== 1) { return null; }
        var cells = head.rows[0].cells, out = [];
        for (var i = 0; i < cells.length; i++) {
            if (cells[i].colSpan > 1 || cells[i].rowSpan > 1) { return null; }
            out.push(cells[i].innerText.replace(/\s+/g, ' ').trim());
        }
        return out;
    }

    function eligible(table) {
        if (table.hasAttribute('data-no-stack') || table.tFoot) { return false; }
        if (table.classList.contains('ds-table--stack') && !table.hasAttribute('data-auto-stack')) { return false; }
        if (table.querySelector('tbody td[colspan]:not(:only-child), tbody td[rowspan], tbody th[rowspan]')) { return false; }
        if (table.querySelector('tbody input:not([type=checkbox]):not([type=hidden]), tbody select, tbody textarea')) { return false; }
        return !!headers(table);
    }

    function stack(table) {
        var labels = headers(table);
        var titleCol = -1, c;
        for (c = 0; c < labels.length && titleCol < 0; c++) {
            if (NAME.test(labels[c]) && !OTHER_LANGUAGE.test(labels[c])) { titleCol = c; }
        }
        for (c = 0; c < labels.length && titleCol < 0; c++) {
            if (labels[c] && !INDEX.test(labels[c]) && !MEDIA.test(labels[c]) && !ACTIONS.test(labels[c])) { titleCol = c; }
        }
        Array.prototype.forEach.call(table.tBodies, function (body) {
            Array.prototype.forEach.call(body.rows, function (row) {
                if (row.cells.length === 1) { return; } // "No records found" row
                Array.prototype.forEach.call(row.cells, function (cell, c) {
                    var label = labels[c] || '';
                    if (INDEX.test(label) || MEDIA.test(label)) { cell.classList.add('ds-hide-sm', 'ds-auto'); return; }
                    if (c === titleCol) { cell.classList.add('ds-stack-title', 'ds-auto'); return; }
                    if (ACTIONS.test(label)) { cell.classList.add('ds-stack-actions', 'ds-auto'); return; }
                    if (label && !cell.hasAttribute('data-label')) { cell.setAttribute('data-label', label); cell.setAttribute('data-auto-label', ''); }
                });
            });
        });
        table.classList.add('ds-table--stack');
        table.setAttribute('data-auto-stack', '');
    }

    function unstack(table) {
        table.classList.remove('ds-table--stack');
        table.removeAttribute('data-auto-stack');
        Array.prototype.forEach.call(table.querySelectorAll('.ds-auto'), function (cell) {
            cell.classList.remove('ds-hide-sm', 'ds-stack-title', 'ds-stack-actions', 'ds-auto');
        });
        Array.prototype.forEach.call(table.querySelectorAll('[data-auto-label]'), function (cell) {
            cell.removeAttribute('data-label');
            cell.removeAttribute('data-auto-label');
        });
    }

    function overflows(table) {
        var box = table.parentElement;
        return box && table.offsetParent !== null && table.scrollWidth > box.clientWidth + 1;
    }

    function run() {
        var tables = document.querySelectorAll('.content-wrapper table.table');
        Array.prototype.forEach.call(tables, function (table) {
            if (table.hasAttribute('data-auto-stack')) { unstack(table); }
            if (PHONE.matches && eligible(table) && overflows(table)) { stack(table); }
        });
    }

    var timer;
    function later() { clearTimeout(timer); timer = setTimeout(run, 150); }

    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', run); } else { run(); }
    window.addEventListener('load', run);
    if (PHONE.addEventListener) { PHONE.addEventListener('change', run); }
    window.addEventListener('orientationchange', later);
    window.addEventListener('resize', later);
    window.dsStackTables = run; // call after inserting table rows with JavaScript
})();
</script>
