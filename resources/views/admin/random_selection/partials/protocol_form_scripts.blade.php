<script>
    document.addEventListener('DOMContentLoaded', function() {
        var periodSelect = document.querySelector('select[name="selection_period"]');
        var monthlyGroup = document.getElementById('monthly-day-group');
        var manualGroup = document.getElementById('manual-dates-group');

        function syncPeriodFields() {
            if (!periodSelect) return;
            if (monthlyGroup) monthlyGroup.style.display = periodSelect.value === 'MONTHLY' ? '' : 'none';
            if (manualGroup) manualGroup.style.display = periodSelect.value === 'MANUAL' ? '' : 'none';
        }

        if (periodSelect) {
            periodSelect.addEventListener('change', syncPeriodFields);
            syncPeriodFields();
        }

        var alternateModeSelect = document.getElementById('alternate_mode');
        var alternatesValueGroup = document.getElementById('alternates-value-group');
        var alternatesTypeGroup = document.getElementById('alternates-type-group');
        var randomizePrintGroup = document.getElementById('randomize-print-order-group');

        function syncAlternateModeFields() {
            if (!alternateModeSelect) return;
            var isImmediate = alternateModeSelect.value === 'immediate';
            if (alternatesValueGroup) alternatesValueGroup.style.display = isImmediate ? '' : 'none';
            if (alternatesTypeGroup) alternatesTypeGroup.style.display = isImmediate ? '' : 'none';
            if (randomizePrintGroup) randomizePrintGroup.style.display = isImmediate ? '' : 'none';
            if (!isImmediate) {
                var valueInput = document.getElementById('alternates_value');
                if (valueInput) valueInput.value = 0;
            }
        }

        if (alternateModeSelect) {
            alternateModeSelect.addEventListener('change', syncAlternateModeFields);
            syncAlternateModeFields();
        }

        var extraEmpty = document.getElementById('extra-tests-empty');
        var subEmpty = document.getElementById('sub-selections-empty');
        var subSelectionCounter = {{ isset($protocol) ? count($protocol->subSelections) : 0 }};

        var addExtra = document.getElementById('add-extra-test');
        if (addExtra) {
            addExtra.addEventListener('click', function() {
                if (document.querySelectorAll('.extra-test').length >= 5) {
                    alert('Maximum 5 extra tests allowed');
                    return;
                }
                var template = document.getElementById('extra-test-template');
                document.getElementById('extra-tests-container').appendChild(template.content.cloneNode(true));
                if (extraEmpty) extraEmpty.style.display = 'none';
            });
        }

        var addSub = document.getElementById('add-sub-selection');
        if (addSub) {
            addSub.addEventListener('click', function() {
                if (document.querySelectorAll('.sub-selection').length >= 3) {
                    alert('Maximum 3 sub-selections allowed');
                    return;
                }
                var template = document.getElementById('sub-selection-template');
                var clone = template.content.cloneNode(true);
                clone.querySelectorAll('[name]').forEach(function(input) {
                    input.setAttribute('name', input.getAttribute('name').replace('[]', '[' + subSelectionCounter + ']'));
                });
                document.getElementById('sub-selections-container').appendChild(clone);
                subSelectionCounter++;
                if (subEmpty) subEmpty.style.display = 'none';
            });
        }

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-extra-test')) {
                e.target.closest('.extra-test').remove();
                if (extraEmpty && document.querySelectorAll('.extra-test').length === 0) {
                    extraEmpty.style.display = '';
                }
            }
            if (e.target.classList.contains('remove-sub-selection')) {
                e.target.closest('.sub-selection').remove();
                if (subEmpty && document.querySelectorAll('.sub-selection').length === 0) {
                    subEmpty.style.display = '';
                }
            }
            if (e.target.classList.contains('add-date')) {
                var wrap = document.createElement('div');
                wrap.className = 'input-group mb-2';
                wrap.style.maxWidth = '320px';
                wrap.innerHTML =
                    '<input type="date" class="form-control" name="manual_dates[]">' +
                    '<div class="input-group-append">' +
                    '<button class="btn btn-outline-secondary remove-date" type="button">−</button>' +
                    '</div>';
                document.getElementById('manual-dates-container').appendChild(wrap);
            }
            if (e.target.classList.contains('remove-date')) {
                e.target.closest('.input-group').remove();
            }
        });
    });
</script>
