# <img height="64" src="https://github.com/DavyCraft648/ItemLifeTime/blob/main/icon.png" width="64"/> ItemLifeTime
Adjust the time of dropped items to disappear in your server

## How does this plugin work?
Default time of dropped items to disappear in PocketMine is 5 minutes (300 seconds),
but you can change it with this plugin!

## How to use?
- `item-lifetime` in config.yml is used to change dropped items' time to disappear in your whole server.


- Use `worlds` in the config if you want a separated item lifetime on each world in your server,
  Example:
  ```yaml
  worlds:
    MyPrettyWorld: 120 # Set item lifetime on MyPrettyWorld world to 120 second
    MyDirtyWorld: 360 # Set item lifetime on MyDirtyWorld world to 360 second
  ```

## Config
```yaml
--- # ItemLifeTime plugin configuration file

# Set dropped item lifetime in your server (in second)
# Default is 300 seconds (5 minutes)
# Minimum value is 0, maximum is 1938
# Item will never despawn if this set to -1
item-lifetime: 300

# Set different custom item lifetime on each of these worlds (optional)
# Value in item-lifetime will be used if the world is not set in this list
# Example configuration:
# worlds:
#   MyPrettyWorld: 120
#   MyDirtyWorld: 360
#
# This will set item lifetime in MyPrettyWorld world to 120 seconds, 360 seconds in MyDirtyWorld
worlds: {}

# Show remaining time to disappear in item's nametag
display-time:
  enabled: false
  # Text to be set in item's nametag
  # {MINUTE}, {SECOND}, {TIME}
  # Set this to "{MINUTE}:{SECOND} {TIME}" will be shown as "01:20 01 minute" (example)
  text: "{MINUTE}:{SECOND}"
...

```
