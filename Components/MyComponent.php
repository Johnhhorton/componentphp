<?php

namespace Components;

use ComponentPHP\Component;

class MyComponent extends Component
{
    public $props = [
        'message' => 'default',
        'shout' => '',
    ];

    //First lifecycle hook
    public function parseProps($props){
        $props['message'] = $props['message'] . '!!!';
        return $props;
    }

    //last lifecycle hook before template creation
    public function created(){

    }

    public function myMethod($input){
        return $input;
    }
}

?>
<div>{{ myMethod('input') }}</div>
