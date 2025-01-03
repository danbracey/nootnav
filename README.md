
# NootNav
![Nootnoot](https://cdn3.emoji.gg/emojis/Nootnoot.gif)

This Discord Bot aims to increase awareness against the extinction of penguins via the tracking of their movements. The name 'NootNav' is reference to the 'Noot Noot' sound made by Pingu.

Thanks to the people at [EcosystemSentinels](https://ecosystemsentinels.org/live-penguin-tracking/), Katie Holt and Eric Wagner for providing the data available for this project via [Wildlife Computers](https://wildlifecomputers.com/). This project will fetch data from their API every month, and will display tracking data on a map. Upon Bot startup, each penguin is given a unique friendly name, that stays with them until the data set is renewed. If you wish to change the names of the penguins, delete the storage/location_data.json file, and new names will be generated based on the gender of the penguin.

## Commands
| Command   | Description                                              |
|-----------|----------------------------------------------------------|
| /penguins | Lists the names of all the penguins                      |
| /track    | Track a penguin! Do /penguins to list available penguins |


## Installation

Requirements:

- PHP >= 8.2
- Composer
  The following PHP extensions are also required:

- fileinfo
- sodium (to build for production)

Once the repository has been cloned, change directory into the repository. Run the following commands:
```
cp .env.example .env
```
Fill in the Discord token, and get a Google Maps API key from Google Cloud, with Static Maps API enabled. Fill this in as well, and then run:

```
php laracord bot:boot --no-migrate
```
The --no-migrate flag is important as otherwise Laracord will throw errors as the database portion has been removed (The app uses flat files for storage, that only need to be updated once a month). The Laracord developers are aware of the issue.

## Initial Configuration
In order to run the Bot, you will have to create an Application using the Discord Developer Portal and obtain a bot token. The Bot does NOT require any Intents to run.

## Screenshots
/penguins command:  
![Tracking Page](https://i.imgur.com/dr8KMz0.png)  

/track command:  
![Listing Penguins](https://i.imgur.com/tDRbxQC.png)
