<?php

return [

    /*
     * The disk where files should be uploaded.
     */
    'disk'  => 'private',

    /*
     * The queue used to perform image conversions.
     * Leave empty to use the default queue driver.
     */
    'queue' => null,

    /*
     * The fully qualified class name of the media model.
     */
    'model' => Auresbug\Media\Models\Media::class,

];
