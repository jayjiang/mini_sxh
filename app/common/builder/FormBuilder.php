<?php
/**
 * +----------------------------------------------------------------------
 * | 表单快速构造器
 * +----------------------------------------------------------------------
 *                      .::::.
 *                    .::::::::.            | AUTHOR: siyu
 *                    :::::::::::           | EMAIL: 407593529@qq.com
 *                 ..:::::::::::'           | QQ: 407593529
 *             '::::::::::::'               | WECHAT: zhaoyingjie4125
 *                .::::::::::               | DATETIME: 2019/07/22
 *           '::::::::::::::..
 *                ..::::::::::::.
 *              ``::::::::::::::::
 *               ::::``:::::::::'        .:::.
 *              ::::'   ':::::'       .::::::::.
 *            .::::'      ::::     .:::::::'::::.
 *           .:::'       :::::  .:::::::::' ':::::.
 *          .::'        :::::.:::::::::'      ':::::.
 *         .::'         ::::::::::::::'         ``::::.
 *     ...:::           ::::::::::::'              ``::.
 *   ```` ':.          ':::::::::'                  ::::..
 *                      '.:::::'                    ':'````..
 * +----------------------------------------------------------------------
 */
namespace app\common\builder;

use think\facade\Request;
use think\facade\Route;
use think\facade\View;

class FormBuilder
{
    /**
     * @var string 模板路径(默认使用系统内置路径，无需设置)
     */
    private $_template = '';

    /**
     * @var array 模板变量
     */
    private $_vars = [
        'page_tips'       => '',    // 页面提示
        'tips_type'       => '',    // 提示类型
        'form_url'        => '',    // 表单提交地址
        'form_method'     => 'post',// 表单提交方式
        'empty_tips'      => '暂无数据',// 没有表单项时的提示信息
        'btn_hide'        => [],    // 要隐藏的按钮
        'btn_title'       => [],    // 按钮标题
        'btn_extra'       => [],    // 额外按钮
        'extra_html'      => '',    // 额外HTML代码
        'extra_js'        => '',    // 额外JS代码
        'extra_css'       => '',    // 额外CSS代码
        'submit_confirm'  => false, // 提交确认
        'form_items'      => [],    // 表单项目
    ];

    /**
     * @var bool 是否分组数据 [分组时不再需要传递其他参数]
     */
    private $_is_group = false;

    /**
     * @var 单例模式句柄
     */
    private static $instance;

    /**
     * 获取句柄
     * @return FormBuilder
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 私有化构造函数
     */
    private function __construct()
    {
        // 初始化
        $this->initialize();
    }

    /**
     * 初始化
     */
    protected function initialize()
    {
        // 设置默认模版
        $this->_template = 'form_builder/layout';
        // 设置默认表单提交地址 [默认为当前方法 + Post]
        $this->_vars['form_url'] = Route::buildUrl(Request::action() . 'Post');
    }

    /**
     * 私有化clone函数
     */
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * 设置表单页提示信息
     * @param string $tips 提示信息
     * @param string $type 提示类型：danger,info,warning,success
     * @param string $pos  提示位置：top,search,bottom
     * @return $this
     */
    public function setPageTips($tips = '', $type = 'info', $pos = 'top')
    {
        if ($tips != '') {
            $this->_vars['page_tips_' . $pos] = $tips;
            $this->_vars['tips_type'] = trim($type) ?? 'info';
        }
        return $this;
    }

    /**
     * 设置表单提交地址
     * @param string $form_url 提交地址
     * @return $this
     */
    public function setFormUrl($form_url = '')
    {
        if ($form_url != '') {
            $this->_vars['form_url'] = trim($form_url);
        }
        return $this;
    }

    /**
     * 设置表单提交方式
     * @param string $value 提交方式
     * @return $this
     */
    public function setFormMethod($value = '')
    {
        if ($value != '') {
            $this->_vars['form_method'] = $value;
        }
        return $this;
    }

    /**
     * 模板变量赋值
     * @param mixed  $name  要显示的模板变量
     * @param string $value 变量的值
     * @return $this
     */
    public function assign($name, $value = '')
    {
        if (is_array($name)) {
            $this->_vars = array_merge($this->_vars, $name);
        } else {
            $this->_vars[$name] = $value;
        }
        return $this;
    }

    /**
     * 隐藏按钮
     * @param array|string $btn 要隐藏的按钮，如：['submit']，其中'submit'->确认按钮，'back'->返回按钮
     * @return $this
     */
    public function hideBtn($btn = [])
    {
        if (!empty($btn)) {
            $this->_vars['btn_hide'] = is_array($btn) ? $btn : explode(',', $btn);
        }
        return $this;
    }

    /**
     * 设置按钮标题
     * @param string|array $btn 按钮名 'submit' -> “提交”，'back' -> “返回”
     * @param string $title 按钮标题
     * @return $this
     */
    public function setBtnTitle($btn = '', $title = '')
    {
        if (!empty($btn)) {
            if (is_array($btn)) {
                $this->_vars['btn_title'] = $btn;
            } else {
                $this->_vars['btn_title'][trim($btn)] = trim($title);
            }
        }
        return $this;
    }

    /**
     * 添加额外按钮
     * @param string $btn 按钮内容
     * @return $this
     */
    public function addBtn($btn = '')
    {
        if ($btn != '') {
            $this->_vars['btn_extra'][] = $btn;
        }
        return $this;
    }

    /**
     * 设置额外HTML代码
     * @param string $extra_html 额外HTML代码
     * @param string $pos        位置 [top和bottom]
     * @return $this
     */
    public function setExtraHtml($extra_html = '', $pos = '')
    {
        if ($extra_html != '') {
            $pos != '' && $pos = '_'.$pos;
            $this->_vars['extra_html'.$pos] = $extra_html;
        }
        return $this;
    }

    /**
     * 设置额外JS代码
     * @param string $extra_js 额外JS代码
     * @return $this
     */
    public function setExtraJs($extra_js = '')
    {
        if ($extra_js != '') {
            $this->_vars['extra_js'] = $extra_js;
        }
        return $this;
    }

    /**
     * 设置额外CSS代码
     * @param string $extra_css 额外CSS代码
     * @return $this
     */
    public function setExtraCss($extra_css = '')
    {
        if ($extra_css != '') {
            $this->_vars['extra_css'] = $extra_css;
        }
        return $this;
    }

    /**
     * 设置提交表单时显示确认框
     * @return $this
     */
    public function submitConfirm()
    {
        $this->_vars['submit_confirm'] = true;
        return $this;
    }

    /**
     * 添加单行文本框
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param array  $group       标签组，可以在文本框前后添加按钮或者文字
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addText($name = '', $title = '', $tips = '', $default = '', $group = [], $extra_attr = '', $extra_class = '', $placeholder = '', $required = false)
    {
        $item = [
            'type'        => 'text',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'group'       => $group,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'placeholder' => !empty($placeholder) ? $placeholder : '请输入' . $title,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加多行文本框
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addTextarea($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false)
    {
        $item = [
            'type'        => 'textarea',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'placeholder' => !empty($placeholder) ? $placeholder : '请输入' . $title,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加单选
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param array  $options     单选数据
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param bool   $required    是否必选
     * @return $this|array
     */
    public function addRadio($name = '', $title = '', $tips = '', $options = [], $default = '', $extra_attr = '', $extra_class = '', $required = false)
    {
        $item = [
            'type'        => 'radio',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'options'     => $options == '' ? [] : $options,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加复选框
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param array $options      复选框数据
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param bool   $required    是否必选
     * @return $this|array
     */
    public function addCheckbox($name = '', $title = '', $tips = '', $options = [], $default = '', $extra_attr = '', $extra_class = '', $required = false)
    {
        $item = [
            'type'        => 'checkbox',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'options'     => $options == '' ? [] : $options,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加表单项 [别名方法]
     * @param string $type 表单项类型
     * @param string $name 表单项名，与各自方法中的参数一致
     * @return $this
     */
    public function addFormItem($type = '', $name = '')
    {
        if ($type != '') {
            // 获取所有参数值
            $args = func_get_args();
            // 删除数组中的第一个元素（type）
            array_shift($args);
            // 首字符转换为大写并拼接为方法名
            $method = 'add'. ucfirst($type);
            // 调用回调函数
            call_user_func_array([$this, $method], $args);
        }
        return $this;
    }

    /**
     * 添加日期
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $format      日期格式
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addDate($name = '', $title = '', $tips = '', $default = '', $format = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false)
    {
        $item = [
            'type'        => 'date',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'format'      => $format == '' ? 'yyyy-mm-dd' : $format,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'placeholder' => !empty($placeholder) ? $placeholder : '请选择或输入' . $title,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 一次性添加多个表单项
     * @param array $items 表单项
     * @return $this
     */
    public function addFormItems($items = [])
    {
        if (!empty($items)) {
            foreach ($items as $item) {
                call_user_func_array([$this, 'addFormItem'], $item);
            }
        }
        return $this;
    }

    /**
     * 添加分组
     * @param array $groups 分组数据
     * @return mixed
     */
    public function addGroup($groups = [])
    {
        if (is_array($groups) && !empty($groups)) {
            $this->_is_group = true;
            foreach ($groups as &$group) {
                foreach ($group as $key => $item) {
                    // 删除数组中的第一个元素（type）
                    $type = array_shift($item);
                    $group[$key] = call_user_func_array([$this, 'add'.ucfirst($type)], $item);
                }
            }
            $this->_is_group = false;
        }

        $item = [
            'type'    => 'group',
            'options' => $groups
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 渲染模版
     * @param string $template       模板文件名或者内容
     * @param bool   $renderContent  是否渲染内容
     * @return string
     * @throws \Exception
     */
    public function fetch(string $template = '', bool $renderContent = false)
    {
        // 单独设置模板
        if ($template != '') {
            $this->_template = $template;
        }
        View::assign($this->_vars);

        return View::fetch($this->_template, $renderContent);
    }

    /**
     * 生成 form_items Html 【已废弃】
     */
    public function html()
    {
        $result = '';
        $vars = $this->_vars;
        foreach ($vars['form_items'] as $var) {
            $requiredHtml = $var['required'] ? ' *' : '';
            $tipsHtml = $var['tips'] ? ' ' . $var['tips'] : '';
            $groupBeforeHtml = $var['group'][0] ?? '';
            $groupAfterHtml = $var['group'][1] ?? '';
            switch ($var['type']) {
                case 'text':
                    $result .= '<div class="row dd_input_group">
                            <div class="form-group">
                                <label class="col-xs-4 col-sm-2 col-md-2 col-lg-1 control-label dd_input_l">' . $var['title'] . '</label>
                                <div class="col-xs-7 col-sm-6 col-md-4 col-lg-4">
                                    <div class="input-group">
                                    ' . $groupBeforeHtml . '
                                    <input type="text" name="' . $var['name'] . '" class="form-control ' . $var['extra_class'] . '" placeholder="' . $var['placeholder'] . '" value="' . $var['value'] . '" ' . $var['extra_attr'] . '>
                                    ' . $groupAfterHtml . '
                                    </div>
                                </div>
                                <div class="col-xs-1 col-sm-4 col-md-6 col-lg-6 dd_ts">' . $requiredHtml . $tipsHtml . '</div>
                            </div>
                        </div>';
                    break;
                case 'textarea':
                    $result .= '<div class="row dd_input_group">
                            <div class="form-group">
                                <div class="col-xs-11 col-sm-8 col-md-6 col-lg-5">
                                    <label class="text-lable">' . $var['title'] . '</label>
                                    <textarea class="form-control ' . $var['extra_class'] . '" name="' . $var['name'] . '" rows="3" placeholder="' . $var['placeholder'] . '" ' . $var['extra_attr'] . '>' . $var['value'] . '</textarea>
                                </div>
                                <div class="col-xs-1 col-sm-4 col-md-6 col-lg-6 dd_ts">' . $requiredHtml . $tipsHtml . '</div>
                            </div>
                        </div>';
                    break;
                default:
            }
        }
        return $result;
    }

}