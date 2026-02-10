<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FileUploader;

class WebsiteSettings extends Model
{
    protected $guarded = [];

    public function updateWebsiteSettings($request)
    {
        $settingsData = [
            'title'   => $request->title,
            'phone'   => $request->phone,
            'address' => $request->address,
        ];

        if ($request->hasFile('logo')) {
            $settingsData['logo'] =
                FileUploader::upload($request->logo, 'website_settings');
        }

        if ($request->hasFile('favicon')) {
            $settingsData['favicon'] =
                FileUploader::upload($request->favicon, 'website_settings');
        }

        return $this->update($settingsData);
    }
}
