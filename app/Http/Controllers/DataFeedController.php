<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataFeed;

class DataFeedController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function getDataFeed(Request $request)
    {
        $validated = $request->validate([
            'datatype' => 'required|integer',
            'limit'    => 'nullable|integer|min:1|max:1000',
        ]);

        $dataType = (int) $validated['datatype'];
        $limit    = isset($validated['limit']) ? (int) $validated['limit'] : null;

        $df = new DataFeed();

        return (object)[
            'labels' => $df->getDataFeed($dataType, 'label', $limit),
            'data'   => $df->getDataFeed($dataType, 'data', $limit),
        ];
    }
}
