# Contao Time Tracking Bundle
Provides time tracking functionality for Contao backend users.

## Usage
This bundle only has the code for tracking times - the functionality where the times get saved is your task.
For creating a submit listener, just tag a service with 

`tags:`
` - { name: time_tracking.listener, type: submit }`
