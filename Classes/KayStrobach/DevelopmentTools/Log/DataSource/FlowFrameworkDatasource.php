<?php

namespace KayStrobach\DevelopmentTools\Log\DataSource;

use Clockwork\DataSource\DataSource;
use Clockwork\Request\Request;

class FlowFrameworkDatasource extends DataSource {

    public function resolve(Request $request)
    {
        $request->viewsData[] = array(
            'data' => array(
                'name' => 'FLOW Framework Constants',
                'data' => array(
                    'FLOW_SAPITYPE' => FLOW_SAPITYPE,
                    'FLOW_PATH_FLOW' => FLOW_PATH_FLOW,
                    'FLOW_PATH_ROOT' => FLOW_PATH_ROOT,
                    'FLOW_PATH_WEB' => FLOW_PATH_WEB,
                    'FLOW_PATH_CONFIGURATION' => FLOW_PATH_CONFIGURATION,
                    'FLOW_PATH_DATA' => FLOW_PATH_DATA,
                    'FLOW_PATH_PACKAGES' => FLOW_PATH_PACKAGES,
                    'FLOW_VERSION_BRANCH' => FLOW_VERSION_BRANCH,
                )
            )
        );
        return parent::resolve($request);
    }
}