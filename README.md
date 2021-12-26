# <img height="64" src="https://github.com/DavyCraft648/ItemLifeTime/blob/main/icon.png" width="64"/> ItemLifeTime
Set custom dropped item lifetime in your server

## How does this works?
Default lifetime for dropped items is 5 minutes (300 seconds)
but you can change it with this plugin!

## How to use?
- `item-lifetime` in config.yml is used to change the lifetime in your whole server.


- Use `worlds` in the config if you want a separated item lifetime on each world in your server,
  Example:
  ```yaml
  worlds:
    MyPrettyWorld: 120 # Set item lifetime on MyPrettyWorld world to 120 second
    MyBadWorld: 360 # Set item lifetime on MyBadWorld world to 360 second
  ```

## Config
```yaml
--- # ItemLifeTime plugin configuration file

# Set dropped item lifetime in your server (in second)
# Default is 5 minutes (300 seconds)
# Minimum value is 0, maximum is 1938
# Item will never despawn if this set to -1
item-lifetime: 300

# Set different custom item lifetime on each of these worlds (optional)
# Example configuration:
# worlds:
#   MyPrettyWorld: 120
#   MyBadWorld: 360
#
# This will set item lifetime in MyPrettyWorld world to 120 seconds, 360 seconds in MyBadWorld, else 300
# seconds in the other worlds
worlds: {}
...

```
