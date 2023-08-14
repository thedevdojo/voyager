<?php

namespace App\VoyagerFormFields;

use App\Models\Chercheur;
use TCG\Voyager\FormFields\AbstractHandler;

class ChercheurDropdown extends AbstractHandler
{
    protected $codename = 'chercheur_dropdown';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        $options->options = Chercheur::pluck('nom');

        return view('voyager::formfields.chercheur_dropdown', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }

}
