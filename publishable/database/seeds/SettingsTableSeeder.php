<?php
use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Setting;

class SettingsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     */
    public function run()
    {
        $setting = $this->findSetting('site.title');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => '网站标题',
                'value'        => '北京XXX网络科技有限公司',
                'details'      => '',
                'type'         => 'text',
                'order'        => 1,
                'group'        => 'Site',
            ])->save();
        }
        $setting = $this->findSetting('site.description');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => '网站描述',
                'value'        => '北京XXX网络科技有限公司是国内优秀的XX行业的企业，生产的XX产品热销海外',
                'details'      => '',
                'type'         => 'text',
                'order'        => 2,
                'group'        => 'Site',
            ])->save();
        }
        $setting = $this->findSetting('site.logo');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => '网站logo',
                'value'        => '',
                'details'      => '',
                'type'         => 'image',
                'order'        => 3,
                'group'        => 'Site',
            ])->save();
        }
        $setting = $this->findSetting('admin.bg_image');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => '系统背景图',
                'value'        => '',
                'details'      => '',
                'type'         => 'image',
                'order'        => 5,
                'group'        => 'Admin',
            ])->save();
        }
        $setting = $this->findSetting('admin.title');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => '系统标题',
                'value'        => 'Face',
                'details'      => '',
                'type'         => 'text',
                'order'        => 1,
                'group'        => 'Admin',
            ])->save();
        }
        $setting = $this->findSetting('admin.description');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => '系统描述',
                'value'        => '基于Laravel的系统管理',
                'details'      => '',
                'type'         => 'text',
                'order'        => 2,
                'group'        => 'Admin',
            ])->save();
        }
        $setting = $this->findSetting('admin.loader');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => '系统Loading图',
                'value'        => '',
                'details'      => '',
                'type'         => 'image',
                'order'        => 3,
                'group'        => 'Admin',
            ])->save();
        }
        $setting = $this->findSetting('admin.icon_image');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => '系统Logo',
                'value'        => '',
                'details'      => '',
                'type'         => 'image',
                'order'        => 4,
                'group'        => 'Admin',
            ])->save();
        }
    }
    /**
     * [setting description].
     *
     * @param [type] $key [description]
     *
     * @return [type] [description]
     */
    protected function findSetting($key)
    {
        return Setting::firstOrNew(['key' => $key]);
    }
}
