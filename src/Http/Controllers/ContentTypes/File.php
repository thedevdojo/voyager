<?php

namespace TCG\Voyager\Http\Controllers\ContentTypes;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class File extends BaseType
{
    /**
     * @return string
     */
    public function handle()
    {
        if (!$this->request->has($this->row->field)) {
            return json_encode([]);
        }

        $files = Arr::wrap($this->request->file($this->row->field));

        $filesPath = [];

        foreach ($files as $file) {
            $filename = $this->generateFileName();
            $path = $this->generatePath();
            $file->storeAs(
                $path,
                $filename.'.'.$file->getClientOriginalExtension(),
                config('voyager.storage.disk', 'public')
            );

            array_push($filesPath, [
                'download_link' => $path.$filename.'.'.$file->getClientOriginalExtension(),
                'original_name' => $file->getClientOriginalName(),
            ]);
        }

        return json_encode($filesPath);
    }

    /**
     * @return string
     */
    protected function generatePath()
    {
        return getUploadPath($this->request, config('voyager.storage.path', '%slug%/%date:F%/%date:Y%/'));
    }

    /**
     * @return string
     */
    protected function generateFileName()
    {
        return Str::random(20);
    }
}
