<?php

namespace FPrintingVendor;

if (!\function_exists('FPrintingVendor\\WPDeskInitWpNoticeAjaxHandler')) {
    /**
     * Init notices AJAX Handler.
     *
     * @param string|null $assetsUrl
     *
     * @return \WPDesk\Notice\AjaxHandler
     */
    function WPDeskInitWpNoticeAjaxHandler($assetsUrl = null)
    {
        $ajax_handler = new \FPrintingVendor\WPDesk\Notice\AjaxHandler($assetsUrl);
        $ajax_handler->hooks();
        return $ajax_handler;
    }
}
if (!\function_exists('FPrintingVendor\\wpdesk_init_wp_notice_ajax_handler')) {
    /**
     * Alias for {@see WPDeskInitNoticeAjaxHandler()} function.
     *
     * @param null $assetsUrl
     *
     * @return \WPDesk\Notice\AjaxHandler
     */
    function wpdesk_init_wp_notice_ajax_handler($assetsUrl = null)
    {
        return \FPrintingVendor\WPDeskInitWpNoticeAjaxHandler($assetsUrl);
    }
}
if (!\function_exists('FPrintingVendor\\WPDeskWpNotice')) {
    /**
     * Creates Notice.
     *
     * @param string $noticeContent Notice content.
     * @param string $noticeType Notice type.
     * @param bool $dismissible Dismissible notice.
     * @param int $priority Notice priority,
     *
     * @return \WPDesk\Notice\Notice
     */
    function WPDeskWpNotice($noticeContent, $noticeType = 'info', $dismissible = \false, $priority = 10)
    {
        return \FPrintingVendor\WPDesk\Notice\Factory::notice($noticeContent, $noticeType, $dismissible, $priority);
    }
}
if (!\function_exists('FPrintingVendor\\wpdesk_wp_notice')) {
    /**
     * Creates Notice.
     *
     * Alias for {@see WPDeskNotice()} function.
     *
     * @param string $noticeContent Notice content.
     * @param string $noticeType Notice type.
     * @param bool $dismissible Dismissible notice.
     * @param int $priority Notice priority,
     *
     * @return \WPDesk\Notice\Notice
     */
    function wpdesk_wp_notice($noticeContent, $noticeType = 'info', $dismissible = \false, $priority = 10)
    {
        return \FPrintingVendor\WPDeskWpNotice($noticeContent, $noticeType, $dismissible, $priority);
    }
}
if (!\function_exists('FPrintingVendor\\WPDeskWpNoticeInfo')) {
    /**
     * Creates Notice Info.
     *
     * @param string $noticeContent Notice content.
     * @param bool $dismissible Dismissible notice.
     * @param int $priority Notice priority,
     *
     * @return \WPDesk\Notice\Notice
     */
    function WPDeskWpNoticeInfo($noticeContent, $dismissible = \false, $priority = 10)
    {
        return \FPrintingVendor\WPDesk\Notice\Factory::notice($noticeContent, \FPrintingVendor\WPDesk\Notice\Notice::NOTICE_TYPE_INFO, $dismissible, $priority);
    }
}
if (!\function_exists('FPrintingVendor\\wpdesk_wp_notice_info')) {
    /**
     * Creates Notice Info.
     *
     * Alias for {@see WPDeskNoticeInfo()} function.
     *
     * @param string $noticeContent Notice content.
     * @param bool $dismissible Dismissible notice.
     * @param int $priority Notice priority,
     *
     * @return \WPDesk\Notice\Notice
     */
    function wpdesk_wp_notice_info($noticeContent, $dismissible = \false, $priority = 10)
    {
        return \FPrintingVendor\WPDeskWpNoticeInfo($noticeContent, $dismissible, $priority);
    }
}
if (!\function_exists('FPrintingVendor\\WPDeskWpNoticeError')) {
    /**
     * Creates Notice Error.
     *
     * @param string $noticeContent Notice content.
     * @param bool $dismissible Dismissible notice.
     * @param int $priority Notice priority,
     *
     * @return \WPDesk\Notice\Notice
     */
    function WPDeskWpNoticeError($noticeContent, $dismissible = \false, $priority = 10)
    {
        return \FPrintingVendor\WPDesk\Notice\Factory::notice($noticeContent, \FPrintingVendor\WPDesk\Notice\Notice::NOTICE_TYPE_ERROR, $dismissible, $priority);
    }
}
if (!\function_exists('FPrintingVendor\\wpdesk_wp_notice_error')) {
    /**
     * Creates Notice Error.
     *
     * Alias for {@see WPDeskNoticeError()} function.
     *
     * @param string $noticeContent Notice content.
     * @param bool $dismissible Dismissible notice.
     * @param int $priority Notice priority,
     *
     * @return \WPDesk\Notice\Notice
     */
    function wpdesk_wp_notice_error($noticeContent, $dismissible = \false, $priority = 10)
    {
        return \FPrintingVendor\WPDeskWpNoticeError($noticeContent, $dismissible, $priority);
    }
}
if (!\function_exists('FPrintingVendor\\WPDeskWpNoticeWarning')) {
    /**
     * Creates Notice Warning.
     *
     * @param string $noticeContent Notice content.
     * @param bool $dismissible Dismissible notice.
     * @param int $priority Notice priority,
     *
     * @return \WPDesk\Notice\Notice
     */
    function WPDeskWpNoticeWarning($noticeContent, $dismissible = \false, $priority = 10)
    {
        return \FPrintingVendor\WPDesk\Notice\Factory::notice($noticeContent, \FPrintingVendor\WPDesk\Notice\Notice::NOTICE_TYPE_WARNING, $dismissible, $priority);
    }
}
if (!\function_exists('FPrintingVendor\\wpdesk_wp_notice_warning')) {
    /**
     * Creates Notice Warning.
     *
     * Alias for {@see WPDeskNoticeWarning()} function.
     *
     * @param string $noticeContent Notice content.
     * @param bool $dismissible Dismissible notice.
     * @param int $priority Notice priority,
     *
     * @return \WPDesk\Notice\Notice
     */
    function wpdesk_wp_notice_warning($noticeContent, $dismissible = \false, $priority = 10)
    {
        return \FPrintingVendor\WPDeskWpNoticeWarning($noticeContent, $dismissible, $priority);
    }
}
if (!\function_exists('FPrintingVendor\\WPDeskWpNoticeSuccess')) {
    /**
     * Creates Notice Success.
     *
     * @param string $noticeContent Notice content.
     * @param bool $dismissible Dismissible notice.
     * @param int $priority Notice priority,
     *
     * @return \WPDesk\Notice\Notice
     */
    function WPDeskWpNoticeSuccess($noticeContent, $dismissible = \false, $priority = 10)
    {
        return \FPrintingVendor\WPDesk\Notice\Factory::notice($noticeContent, \FPrintingVendor\WPDesk\Notice\Notice::NOTICE_TYPE_SUCCESS, $dismissible, $priority);
    }
}
if (!\function_exists('FPrintingVendor\\wpdesk_wp_notice_success')) {
    /**
     * Creates Notice Success.
     *
     * Alias for {@see WPDeskNoticeSuccess()} function.
     *
     * @param string $noticeContent Notice content.
     * @param bool $dismissible Dismissible notice.
     * @param int $priority Notice priority,
     *
     * @return \WPDesk\Notice\Notice
     */
    function wpdesk_wp_notice_success($noticeContent, $dismissible = \false, $priority = 10)
    {
        return \FPrintingVendor\WPDeskWpNoticeSuccess($noticeContent, $dismissible, $priority);
    }
}
if (!\function_exists('FPrintingVendor\\WPDeskPermanentDismissibleWpNotice')) {
    /**
     * Creates Permanent Dismissible Notice.
     *
     * @param string $noticeContent Notice content.
     * @param string $noticeType Notice type.
     * @param string $noticeName Notice name.
     * @param int $priority Notice priority.
     *
     * @return \WPDesk\Notice\Notice
     */
    function WPDeskPermanentDismissibleWpNotice($noticeContent, $noticeName, $noticeType = 'info', $priority = 10)
    {
        return \FPrintingVendor\WPDesk\Notice\Factory::permanentDismissibleNotice($noticeContent, $noticeName, $noticeType, $priority);
    }
}
if (!\function_exists('FPrintingVendor\\wpdesk_permanent_dismissible_wp_notice')) {
    /**
     * Creates Permanent Dismissible Notice.
     *
     * Alias for {@see WPDeskPermanentDismissibleNotice()} function.
     *
     * @param string $noticeContent Notice content.
     * @param string $noticeName Notice name.
     * @param string $noticeType Notice type.
     * @param int $priority Notice priority.
     *
     * @return \WPDesk\Notice\Notice
     */
    function wpdesk_permanent_dismissible_wp_notice($noticeContent, $noticeName, $noticeType = 'info', $priority = 10)
    {
        return \FPrintingVendor\WPDeskPermanentDismissibleWpNotice($noticeContent, $noticeName, $noticeType, $priority);
    }
}
