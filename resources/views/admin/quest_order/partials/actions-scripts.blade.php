@push('scripts')
<script>
(function ($) {
    'use strict';

    function positionFloatingMenu($toggle, $menu) {
        var offset = $toggle.offset();
        var menuWidth = $menu.outerWidth();
        var viewportWidth = $(window).width();
        var padding = 8;
        var left = offset.left + $toggle.outerWidth() - menuWidth;

        if (left < padding) {
            left = padding;
        }
        if (left + menuWidth > viewportWidth - padding) {
            left = Math.max(padding, viewportWidth - menuWidth - padding);
        }

        $menu.css({
            display: 'block',
            position: 'absolute',
            top: offset.top + $toggle.outerHeight() + 2,
            left: left,
            right: 'auto',
            zIndex: 1065
        });
    }

    $(document).on('show.bs.dropdown', '.quest-order-actions-group', function () {
        var $group = $(this);
        var $toggle = $group.find('.quest-order-actions-toggle');
        var $menu = $group.find('.quest-order-actions-menu');

        if (!$menu.length) {
            return;
        }

        $menu
            .addClass('quest-order-actions-menu--floating')
            .appendTo('body');

        positionFloatingMenu($toggle, $menu);
        $group.data('questMenu', $menu);
        $group.data('questToggle', $toggle);
    });

    $(document).on('hide.bs.dropdown', '.quest-order-actions-group', function () {
        var $group = $(this);
        var $menu = $group.data('questMenu');

        if (!$menu || !$menu.length) {
            return;
        }

        $menu
            .removeClass('quest-order-actions-menu--floating show')
            .removeAttr('style')
            .appendTo($group);

        $group.removeData('questMenu');
        $group.removeData('questToggle');
    });

    $(window).on('resize.questOrderActions scroll.questOrderActions', function () {
        $('.quest-order-actions-group.show').each(function () {
            var $group = $(this);
            var $menu = $group.data('questMenu');
            var $toggle = $group.data('questToggle');

            if ($menu && $toggle) {
                positionFloatingMenu($toggle, $menu);
            }
        });
    });

    function closeOpenQuestActions() {
        $('.quest-order-actions-toggle[aria-expanded="true"]').dropdown('toggle');
    }

    $(document).on('click', '.quest-order-delete-trigger', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $trigger = $(this);
        var $modal = $('#questOrderDeleteModal');
        var action = $trigger.data('action');
        var label = $trigger.data('label') || 'this order';

        if (!$modal.length || !action) {
            return;
        }

        closeOpenQuestActions();
        $modal.find('.quest-order-modal-label').text(label);
        $modal.find('#questOrderDeleteForm').attr('action', action);
        $modal.modal('show');
    });

    $(document).on('click', '.quest-order-cancel-trigger', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $trigger = $(this);
        var $modal = $('#questOrderCancelModal');
        var action = $trigger.data('action');
        var label = $trigger.data('label') || 'this order';

        if (!$modal.length || !action) {
            return;
        }

        closeOpenQuestActions();
        $modal.find('.quest-order-modal-label').text(label);
        $modal.find('#questOrderCancelForm').attr('action', action);
        $modal.modal('show');
    });
})(jQuery);
</script>
@endpush