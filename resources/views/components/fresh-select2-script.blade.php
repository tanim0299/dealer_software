{{-- Global Select2 + date defaults. Select2: skip data-select2-skip, .dataTables_length, size>1. Dates: empty type=date → today; skip data-date-no-default, readonly, disabled. Call window.initFreshSelect2($c) / window.applyDateInputDefaults($c) after dynamic DOM. --}}
<script>
(function () {
    function todayISO() {
        var d = new Date();
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }

    window.applyDateInputDefaults = function (root) {
        var scope = root && root.querySelectorAll ? root : document;
        var nodes = scope.querySelectorAll('input[type="date"]');
        var today = todayISO();
        nodes.forEach(function (el) {
            if (el.hasAttribute('data-date-no-default')) return;
            if (el.closest('[data-date-no-default]')) return;
            if (el.readOnly || el.disabled) return;
            if (el.value && String(el.value).trim() !== '') return;
            el.value = today;
        });
    };
})();

(function ($) {
    function shouldSkipSelect($el) {
        if ($el.is('[data-select2-skip]')) return true;
        if ($el.closest('[data-select2-skip]').length) return true;
        if ($el.closest('.dataTables_length').length) return true;
        var sz = parseInt($el.attr('size'), 10);
        if (sz > 1) return true;
        return false;
    }

    function isFormStyledSelect($el) {
        var cls = $el.attr('class') || '';
        return /\bform-select\b/.test(cls) || /\bform-control\b/.test(cls);
    }

    window.initFreshSelect2 = function (root) {
        var $scope = root && $(root).length ? $(root) : $(document);
        $scope.find('select').filter(function () {
            var $el = $(this);
            if (!isFormStyledSelect($el)) return false;
            if (shouldSkipSelect($el)) return false;
            if ($el.hasClass('select2-hidden-accessible')) return false;
            return true;
        }).each(function () {
            var $el = $(this);
            var $modal = $el.closest('.modal');
            var hasBlank = $el.find('option[value=""]').length > 0;
            var opts = {
                width: '100%',
                allowClear: hasBlank,
                dropdownAutoWidth: false,
            };
            if (hasBlank) {
                var ph = ($el.find('option[value=""]').first().text() || '').trim();
                if (ph) opts.placeholder = ph;
            }
            if ($modal.length) {
                opts.dropdownParent = $modal;
            }
            if ($el.prop('multiple')) {
                opts.closeOnSelect = false;
            }
            $el.select2(opts);
        });
    };

    $(function () {
        if (typeof window.applyDateInputDefaults === 'function') {
            window.applyDateInputDefaults();
        }
        window.initFreshSelect2();
    });

    $(document).on('shown.bs.modal', '.modal', function () {
        if (typeof window.applyDateInputDefaults === 'function') {
            window.applyDateInputDefaults(this);
        }
        window.initFreshSelect2(this);
    });
})(jQuery);
</script>
