<?php return array (
  'App\\Providers\\EventServiceProvider' => 
  array (
    'App\\Events\\TaskCreated' => 
    array (
      0 => 'App\\Listeners\\SendTaskAssignedNotification',
    ),
    'App\\Events\\TaskUpdated' => 
    array (
      0 => 'App\\Listeners\\SendTaskCompletedNotification',
    ),
    'App\\Events\\ProjectMilestone' => 
    array (
      0 => 'App\\Listeners\\SendProjectMilestoneNotification',
    ),
  ),
  'Illuminate\\Foundation\\Support\\Providers\\EventServiceProvider' => 
  array (
    'App\\Models\\Project' => 
    array (
      0 => 'App\\Listeners\\SendProjectMilestoneNotification@__invoke',
    ),
    'App\\Events\\TaskCreated' => 
    array (
      0 => 'App\\Listeners\\SendTaskAssignedNotification@handle',
    ),
    'App\\Events\\TaskUpdated' => 
    array (
      0 => 'App\\Listeners\\SendTaskCompletedNotification@handle',
    ),
  ),
);