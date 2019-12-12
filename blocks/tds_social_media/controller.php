<?php
/**
 * TDS social media block controller.
 *
 * Copyright 2019 - TDSystem Beratung & Training - Thomas Dausner
 */

namespace Concrete\Package\TdsSocialMedia\Block\TdsSocialMedia;

use Concrete\Core\Block\BlockController;
use Concrete\Core\View\View;
use Concrete\Core\Asset\AssetList;

class Controller extends BlockController
{
    protected $btInterfaceWidth = 600;
    protected $btInterfaceHeight = 720;
    protected $btCacheBlockOutput = true;
    protected $btTable = 'btTdsSocialMedia';
    protected $btDefaultSet = 'social';

    protected $iconStyles = '
		.ccm-block-tds-social-media.block-%b% .icon-container { margin: calc(%iconMargin%px / -2);}
		.ccm-block-tds-social-media.block-%b% .icon-container .svc { margin-top: calc(%iconMargin%px / 2); margin-bottom: calc(%iconMargin%px / 2); }
		.ccm-block-tds-social-media.block-%b% .icon-container .svc.activated span { %activeAttrs% }
		.ccm-block-tds-social-media.block-%b% .social-icon:hover { %hoverAttrs% }
		.ccm-block-tds-social-media.block-%b% .social-icon-color { color: #f8f8f8; background: %iconColor%; }
		.ccm-block-tds-social-media.block-%b% .social-icon-color-inverse { color: %iconColor%; }
		.ccm-block-tds-social-media.block-%b% .social-icon.activated, .ccm-block-tds-social-media .social-icon.activated:hover { %activeAttrs% }
		.ccm-block-tds-social-media.block-%b% .social-icon {	float: left; margin: 0 calc(%iconMargin%px / 2);
														height: %iconSize%px; width: %iconSize%px; border-radius: %borderRadius%px; }
		.ccm-block-tds-social-media.block-%b% .social-icon i.fa {	display: block; font-size: calc(%iconSize%px *.6); text-align: center;
                                                                        width: 100%; padding-top: calc((100% - 1em) / 2); }
	';
    protected $mediaList = [];
    public $mediaType = 'undef';
    protected $bUID = 0;
    protected $align, $iconShape, $iconColor, $iconStyle, $hoverIcon, $activeIcon, $iconMargin, $iconSize, $linkTarget, $titleText;

    public function getBlockTypeDescription()
    {
        return t('Add EU-GDPR compliant social media icons on your pages.');
    }

    public function getBlockTypeName()
    {
        return t('Social Media Icons');
    }

    public function add()
    {
        $this->set('linkTarget', '_self');
        $this->set('align', 'left');
        $this->set('iconStyle', 'logo');
        $this->set('iconColor', '#00f');    /* blue */
        $this->set('iconSize', '20');
        $this->set('hoverIcon', '#ccc');    /* pale gray */
        $this->set('activeIcon', '#ff0');    /* yellow */
        $this->set('iconMargin', '0');
        $this->edit();
    }

    public function edit()
    {
        $this->set('mediaType', $this->mediaType);
        $this->set('mdTypes', [
            'undef' => t('(undefined)'),
            'visit' => t('...visit our page'),
            'share' => t('...share this page'),
        ]);
        $this->set('targets', [
            '_blank' => t('a new window or tab'),
            '_self' => t('the same frame as it was clicked (this is default)'),
            '_parent' => t('the parent frame'),
            '_top' => t('the full body of the window'),
        ]);
        $this->set('orientation', [
            'left' => t('left'),
            'right' => t('right'),
        ]);
        $this->set('iconStyleList', [
            'logo' => t('logo'),
            'logo-inverse' => t('logo inverse'),
            'color' => t('color'),
            'color-inverse' => t('color inverse'),
        ]);
        $this->set('titleTextTemplate', [
            'visit' => t('Visit our page at %s'),
            'share' => t('Share this page at %s'),
        ]);
        $this->set('bubbleTextTemplate', [
            'visit' => t('You now have enabled the icon to visit our page at "%s".' .
                ' If you now click at the activated icon the "visit" page at "%s" shall be opened.' .
                ' On opening your personal browser data is transmitted to the provider "%s".' .
                ' To avoid this you can click at the close <strong>X</strong> button' .
                ' and by this disable the "visit "icon.'),
            'share' => t('You now have enabled the icon to share this page at "%s".' .
                ' If you now click at the activated icon the page at "%s" shall be opened.' .
                ' On opening your personal browser data is transmitted to the provider "%s".' .
                ' To avoid this you can click at the close <strong>X</strong> button' .
                ' and by this disable the share icon.'),
        ]);

        $this->set('messages', [
            'no_svc_selected' => t('No social media service selected.'),
            'missing_urls' => t('Missing URL(s) for: %s'),
        ]);

        $al = AssetList::getInstance();
        $ph = 'tds_social_media';
        $al->register('css', $ph . '/form', 'blocks/' . $ph . '/css/form.css', [], $ph);
        $al->register('css', $ph . '/view', 'blocks/' . $ph . '/css/view.css', [], $ph);
        $al->registerGroup($ph, [
            ['css', $ph . '/form'],
            ['css', $ph . '/view'],
        ]);
        $v = View::getInstance();
        $v->requireAsset($ph);

        $this->view();
    }

    public function view()
    {
        $this->app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
        if ($this->mediaType != 'undef')
        {
            if (gettype($this->mediaList) == "string")
            {   // add from clipboard --> is array already
                $this->mediaList = unserialize($this->mediaList);
            }
        }
        $this->setupMediaList();
        $this->set('mediaList', $this->mediaList);
        $this->set('bUID', $this->app->make('helper/validation/identifier')->getString(8));
        if ($this->align == null)
        {
            $this->align = 'left';
            $this->set('align', $this->align);
        }
    }

    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset('font-awesome');
        $this->requireAsset('javascript', 'jquery');
    }

    public function save($args)
    {
        $args['iconSize'] = intval($args['iconSize']);
        $args['iconMargin'] = intval($args['iconMargin']);
        $args['mediaList'] = serialize($args['mediaList']);

        parent::save($args);
    }

    public function getIconStyles($bUID)
    {
        return str_replace('%b%', $bUID, $this->iconStyles);
    }

    public function getIconStylesExpanded($bUID)
    {
        $this->bUID = $bUID;
        $borderRadius = $this->iconShape == 'round' ? $this->iconSize / 2 : 0;
        $hoverAttrs = $this->hoverIcon != '' ? "background: $this->hoverIcon;" : '';
        $activeAttrs = $this->activeIcon != '' ? "background-color: $this->activeIcon;" : '';
        return '
<style id="iconStyles-' . $bUID . '" type="text/css">
	' . str_replace(['%b%', '%iconColor%', '%iconMargin%', '%iconSize%', '%borderRadius%', '%hoverAttrs%', '%activeAttrs%'],
                [$bUID, $this->iconColor, $this->iconMargin, $this->iconSize, $borderRadius, $hoverAttrs, $activeAttrs], $this->iconStyles) . '
</style>';
    }

    public function getMediaList()
    {
        return $this->mediaList;
    }

    private function setupMediaList()
    {
        $req = $this->app->make(\Concrete\Core\Http\Request::class);
        $c = $req->getCurrentPage();
        $sitename = $this->app->make('site')->getSite()->getSiteName();
        if (is_object($c) && !$c->isError())
        {
            $title = $c->getCollectionName();
        } else
        {
            $title = $sitename;
        }

        $url = urlencode($req->getUri());
        $body = rawurlencode(t("Check out this article on %s:\n\n%s\n%s", tc('SiteName', $sitename), $title, urldecode($url)));
        $subject = rawurlencode(t('Please notice this article.'));

        $mediaListMaster = [
            'Behance' => ['fa' => 'behance', 'icolor' => '#1769FF', 'ph' => t("https://www.behance.net/your-account-name"),
                'rx' => '^https://(www\.)?behance\.net/[^/]+',
            ],
            'deviantART' => ['fa' => 'deviantart', 'icolor' => '#4E6252', 'ph' => t("https://your-account-name.deviantart.com"),
                'rx' => '^https://[^/]+\.deviantart\.com',
            ],
            'Dribbble' => ['fa' => 'dribbble', 'icolor' => '#EA4C89', 'ph' => t("https://dribbble.com/your-account-name"),
                'rx' => '^https://(www\.)?dribbble\.com/[^/]+',
            ],
            'Facebook' => ['fa' => 'facebook', 'icolor' => '#3B5998', 'ph' => t("https://www.facebook.com/your-account-name"),
                'rx' => '^https://(www\.)?facebook\.com/[^/]+',
                'sa' => "https://www.facebook.com/sharer/sharer.php?u=$url",
            ],
            'Flickr' => ['fa' => 'flickr', 'icolor' => '#000000', 'ph' => t("https://www.flickr.com/photos/your-account-name"),
                'rx' => '^https://(www\.)?flickr\.com/photos/[^/]+',
            ],
            'Github' => ['fa' => 'github', 'icolor' => '#000000', 'ph' => t("https://github.com/your-account-name"),
                'rx' => '^https://(www\.)?github\.com/[^/]+',
            ],
            'Instagram' => ['fa' => 'instagram', 'icolor' => '#517FA4', 'ph' => t("http://instagram.com/your-account-name"),
                'rx' => '^http://(www\.)?instagram\.com/[^/]+',
            ],
            'iTunes' => ['fa' => 'music', 'icolor' => '#0247A4', 'ph' => t("https://itunes.apple.com/..."),
                'rx' => '^https://(www\.)?itunes\.apple\.com/.*',
            ],
            'Linkedin' => ['fa' => 'linkedin', 'icolor' => '#007BB6', 'ph' => t("https://www.linkedin.com/in/your-account-name"),
                'rx' => '^https://(www\.)?linkedin\.com/in/[^/]+',
                'sa' => "https://www.linkedin.com/shareArticle?mini-true&url={$url}&title=" . urlencode($title),
            ],
            'Pinterest' => ['fa' => 'pinterest-p', 'icolor' => '#CB2027', 'ph' => t("https://www.pinterest.com/your-account-name"),
                'rx' => '^https://(www\.)?pinterest\.com/[^/]+',
                'sa' => "https://www.pinterest.com/pin/create/button?url=$url",
            ],
            'Reddit' => ['fa' => 'reddit', 'icolor' => '#FF4500', 
                'sa' => "https://www.reddit.com/submit?url={$url}",
            ],
            'Skype' => ['fa' => 'skype', 'icolor' => '#00AFF0', 'ph' => t("skype:profile_name?your-account-name"),
                'rx' => '^skype:[^/]+\?[^/]+'],
            'SoundCloud' => ['fa' => 'soundcloud', 'icolor' => '#FF3A00', 'ph' => t("https://soundcloud.com/your-account-name"),
                'rx' => '^https://(www\.)?soundcloud\.com/[^/]+',
            ],
            'Spotify' => ['fa' => 'spotify', 'icolor' => '#7AB800', 'ph' => t("https://play.spotify.com/artist/your-account-name"),
                'rx' => '^https://play\.spotify\.com/artist//.*',
            ],
            'Tumblr' => ['fa' => 'tumblr', 'icolor' => '#35465C', 'ph' => t("http://www.your-account-name.tumblr.com"),
                'rx' => '^http://(www\.)?[^/]+\.tumblr\.com',
            ],
            'Twitter' => ['fa' => 'twitter', 'icolor' => '#55ACEE', 'ph' => t("https://twitter.com/your-account-name"),
                'rx' => '^https://(www\.)?twitter\.com/[^/]+',
                'sa' => "https://twitter.com/intent/tweet?url=$url",
            ],
            'Vimeo' => ['fa' => 'vimeo', 'icolor' => '#1AB7EA', 'ph' => t("http://vimeo.com/your-account-name"),
                'rx' => '^http://(www\.)?vimeo\.com/[^/]+',
            ],
            'VK' => ['fa' => 'vk', 'icolor' => '#4E76A7', 
                'sa' => "http://vk.com/share.php?url={$url}&title=" . urlencode($title),
            ],
            'Youtube' => ['fa' => 'youtube', 'icolor' => '#E52D27', 'ph' => t("https://www.youtube.com/user/your-account-name"),
                'rx' => '^https://(www\.)?youtube\.com/user/[^/]+',
            ],
            'Xing' => ['fa' => 'xing', 'icolor' => '#006567', 'ph' => t("https://www.xing.com/profile/your-account-name"),
                'rx' => '^https://(www\.)?xing\.com/profile/[^/]+',
                'sa' => "https://www.xing.com/spi/shares/new?url={$url}",
            ],
            'Print' => ['fa' => 'print', 'icolor' => '#696969', 
                'sa' => 'javascript:window.print();',
            ],
            'Mail' => ['fa' => 'envelope', 'icolor' => '#696969', 
                'sa' => "mailto:?body={$body}&subject={$subject}",
            ],
        ];

        $colors = strpos($this->iconStyle, 'logo') === false;
        $inverse = strpos($this->iconStyle, 'inverse') !== false;
        $blockClass = '	.ccm-block-tds-social-media.block-%b%';
        foreach ($mediaListMaster as $svc => $mProps)
        {
            $this->iconStyles .= $blockClass . ' .social-icon-' . $svc . ' { color: #ffffff; background: ' . $mProps['icolor'] . '; }' . "\n";
            $this->iconStyles .= $blockClass . ' .social-icon-' . $svc . '-inverse { color: ' . $mProps['icolor'] . '; }' . "\n";
            $iconClass = 'social-icon social-icon-';
            $iconClass .= $colors ? 'color' : $svc;
            $iconClass .= $inverse ? '-inverse' : '';

            if (empty($this->mediaList[$svc]))
            {
                $this->mediaList[$svc] = [];
            }
            $props = $this->mediaList[$svc];
            $trg = $this->linkTarget;
            if (array_key_exists('ph', $mProps))
            { #------------------ visit
                if ($svc == 'Skype')
                    $trg = '_self';
                $icon = '<span class="' . $iconClass . '" data-key="' . $svc . '" data-href="' . $props['url'] . '" data-target="' . $trg . '">' .
                    '<i class="fa fa-' . $mProps['fa'] . '" title="' . $title . '"></i>' .
                    '</span>';
                $this->mediaList[$svc]['visit-icon'] = $icon;
            }
            if (array_key_exists('sa', $mProps))
            { #------------------ share
                $title = h(str_replace('%s', $svc, $this->titleText));
                if ($svc == 'Print' || $svc == 'Mail')
                {
                    $title = $svc == 'Print' ? t('Print this page') : t('Share this page by email');
                    $iconClass .= ' local';
                }
                $icon = '<span class="' . $iconClass . '" data-key="' . $svc . '" data-href="' . $mProps['sa'] . '" data-target="' . $trg . '">' .
                    '<i class="fa fa-' . $mProps['fa'] . '" title="' . $title . '"></i>' .
                    '</span>';
                $this->mediaList[$svc]['share-icon'] = $icon;
            }
            if ($props['checked'])
            { # for view ------------------
                $this->mediaList[$svc]['html'] = '
					<div class="svc ' . $mProps['fa'] . '">
					   ' . $this->mediaList[$svc][$this->mediaType. '-icon'] . '
				   </div>';
            }
            $this->mediaList[$svc]['ph'] = $mProps['ph'];
            $this->mediaList[$svc]['rx'] = $mProps['rx'];
            $this->mediaList[$svc]['sa'] = $mProps['sa'];
        }
    }
}
