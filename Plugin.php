<?php

declare(strict_types=1);

namespace TypechoPlugin\AliveTitle;

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

use Typecho\Plugin\PluginInterface;
use Typecho\Widget\Helper\Form;
use Typecho\Widget\Helper\Form\Element\Radio;
use Typecho\Widget\Helper\Form\Element\Text;
use Widget\Options;

/**
 * 让你的标题动起来!&nbsp;
 * 无其他依赖, 采用原生 ES6 编写.
 * 
 * 
 * @package AliveTitle
 * @author 酢豚
 * @version 1.1.0
 * @link https://kazusa.cc
 * 
 * 
 * 0.9.1 修改了实现方法(JQ->ES6),更贴近描述
 * 
 * 0.9.2 简化了代码逻辑
 * 
 * 0.9.5 重构,并引入了新的 bug
 * 
 * 1.0.0 解决了 PJAX 的兼容问题
 * 
 * 1.0.1 增加简单的搜索引擎的爬虫蜘蛛检测,降低对页面抓取结果的影响
 * 
 * 1.0.2 重构代码并使用命名空间, 改为匿名函数, 修正一处拼写错误
 * 
 * 1.1.0 按照官方 <https://joyqi.com/typecho/about-typecho-1-2-dev-plan.html> 推荐的编码风格 <https://www.php-fig.org/psr/psr-12/> 优化代码
 *       TODO: 尝试使用添加路由的方式返回动态 JavaScript 内容
 */
class Plugin implements PluginInterface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     */
    public static function activate()
    {
        \Typecho\Plugin::factory('Widget_Archive')->footer = __CLASS__ . '::aliveTitle';
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     */
    public static function deactivate()
    {
    }

    /**
     * 获取插件配置面板
     *
     * @param Form $form 配置面板
     */
    public static function config(Form $form)
    {
        $titleScroll = new Radio('titleScroll', [True => 'True', False => 'False'], 1, _t('是否启用标题滚动'));

        $scrollSpeed = new Text('scrollSpeed', NULL, '800', _t('标题滚动的速度（毫秒）'), _t('不建议过快'));

        $titleLength = new Text('titleLength', NULL, '12', _t('滚动长度阈值(闭区间)'), _t('取值建议在 6 ~ 15 之间'));

        $titleReplace = new Radio('titleReplace', [True => 'True', False => 'False'], 1, _t('是否启用失焦替换'), _t('关闭则下方相关选项全部失效'));

        $replaceScroll = new Radio('replaceScroll', [True => 'True', False => 'False'], 0, _t('是否启用失焦滚动'));

        $replaceTimeout = new Text('replaceTimeout', NULL, '1200', _t('延时恢复标题'), _t('立即恢复填 0,单位毫秒'));

        $lostFocus = new Text('lostFocus', NULL, '|･ω･｀)你看不见我……你看不见我……你看不见我……', _t('失去焦点时'), _t('当前页面处于不可见时触发,可见时点击页面以外区域不触发'));

        $getFocus = new Text('getFocus', NULL, '_(:3」」还是被发现了', _t('重新获得焦点时'), _t('如不需要和上面保持一致即可'));

        $pjax = new Radio('pjax', [True => 'True', False => 'False'], 0, _t('兼容 PJAX 和 AJAX'), _t('原理是注册一个点击监听,如果不需要不建议启用<br />或者找到自己主题提供的重载接口在 alivetitle.js 中添加并填入 `title = document.title;` 即可生效<br />示例:&nbsp;<code>Aria.reloadAction = () => {<br />title = document.title;<br />}</code>'));

        $form->addInput($titleScroll); // 是否滚动
        $form->addInput($scrollSpeed); // 滚动速度
        $form->addInput($titleLength); // 标题长度
        $form->addInput($titleReplace); // 是否替换
        $form->addInput($replaceScroll); // 是否失焦滚动
        $form->addInput($replaceTimeout); // 替换延时
        $form->addInput($lostFocus); // 失焦文本
        $form->addInput($getFocus); // 重新获焦文本
        $form->addInput($pjax); // 重新获焦文本
    }

    /**
     * 个人用户的配置面板
     *
     * @param Form $form
     */
    public static function personalConfig(Form $form)
    {
    }

    /**
     * 插件实现方法
     *
     * @access public
     * @return void
     */
    public static function aliveTitle()
    {
        $options = Options::alloc()->plugin('AliveTitle');

        $script = sprintf(
            '<script
    async
    data-scroll="%s"
    data-scroll-speed="%s"
    data-title-length="%s"
    data-replace="%s"
    data-replace-scroll="%s"
    data-replace-timeout="%s"
    data-lost-focus="%s"
    data-get-focus="%s"
    data-pjax="%s"
    type="text/javascript"
    src="%s/AliveTitle/alivetitle.js">
</script>',
            $options->titleScroll,
            $options->scrollSpeed,
            $options->titleLength,
            $options->titleReplace,
            $options->replaceScroll,
            $options->replaceTimeout,
            $options->lostFocus,
            $options->getFocus,
            $options->pjax,
            __TYPECHO_PLUGIN_DIR__
        );

        // 避免搜索引擎收录问题
        $keywords = ['baidu', 'spider', 'bot', 'google', 'https://'];
        foreach ($keywords as $keyword) {
            if (stripos($_SERVER['HTTP_USER_AGENT'], $keyword) !== false) {
                // 直接结束插件函数
                return;
            }
        }

        echo $script;
    }
}
