<p align="center">
    <img src=".github/.media/logo.png" width="144" height="144" alt="FancyGenerators plugin logo">
</p>

<h1 align="center">FancyGenerators</h1>
<p align="center">A collection of new level Generators</p>

<p align="center">
    <a href="https://poggit.pmmp.io/p/FancyGenerators">
        <img src="https://poggit.pmmp.io/shield.state/FancyGenerators" alt="Plugin version">
    </a>
    <a href="https://github.com/pmmp/PocketMine-MP">
        <img src="https://poggit.pmmp.io/shield.api/FancyGenerators" alt="API version">
    </a>
    <a href="https://poggit.pmmp.io/p/FancyGenerators">
        <img src="https://poggit.pmmp.io/shield.dl/FancyGenerators" alt="Downloads on Poggit">
    </a>
    <a href="https://github.com/survanetwork/FancyGenerators/blob/master/LICENSE">
        <img src="https://img.shields.io/github/license/survanetwork/FancyGenerators.svg" alt="License">
    </a>
    <a href="https://discord.gg/t4Kg4j3829">
        <img src="https://img.shields.io/discord/685532530451283997?color=blueviolet" alt="Discord">
    </a>
    <a href="https://plugins.surva.net">
        <img src="https://img.shields.io/badge/website-visit-ee8031" alt="Website">
    </a>
</p>

<p align="center">
    <a href="https://plugins.surva.net/#fancygenerators">
        <img src="https://static.surva.net/osplugins/assets/dl-buttons/fancygenerators.png" width="220" height="auto" alt="Download FancyGenerators plugin release">
        <img src="https://static.surva.net/osplugins/assets/feature-banners/fancygenerators.png" width="650" height="auto" alt="FancyGenerators plugin features">
    </a>
</p>

## 📙 Description
FancyGenerators is an experimental plugin project which adds new kinds of world generators to your PocketMine-MP server.
We've implemented some well-known world generators like void with better performance.
We've also added some crazy new world generators creating fantasy worlds.
You can use any compatible world management plugin, e.g. [Worlds by surva](https://plugins.surva.net/#worlds).

## 🎁 Features
- **COMMON WORLD GENERATORS** Adds well-known world generators like void with great performance
- **SPECIAL WORLD GENERATORS** Create worlds like never before with our special, crazy world generators
- **COMPATIBILITY** Using the standard API, compatible with every modern world management plugin

## 🗺️ Generators

### Void

The void generator just generates an empty world without any blocks, but a small 2x2 planks
platform at the spawn.

<img src=".github/.media/screenshots/void.png" width="450" height="auto" alt="Void generator world screenshot">

### CandyLand

CandyLand creates a very colorful world with clay blocks in random colors and
custom colorful trees, also made out of clay blocks. However, it is also the generator
with the worst performance yet, so world generation requires a bit more computing power than
usual (but still runs on most computers).

<img src=".github/.media/screenshots/candyland.png" width="450" height="auto" alt="CandyLand generator world screenshot">

### WinterWonder

WinterWonder generates a beautiful Christmas-themed world with snow-blocks mixed with red and
green decorations of wool. The world is populated with Christmas trees (made of green wool and torches)
and gifts in random colors.

<img src=".github/.media/screenshots/winterwonder.png" width="450" height="auto" alt="WinterWonder generator world screenshot">

### PirateIslands

PirateIslands creates a huge sea with a constant water level and ground made out of sand and sandstone.
It randomly spawns hilly islands in the water with terrain made out of sandstone and jungle
trees on the islands.

<img src=".github/.media/screenshots/pirateislands.png" width="450" height="auto" alt="PirateIslands generator world screenshot">

## 🖱 Usage
Just drop the plugin file into your server's plugin folder, there is no further configuration or commands required.

You can use a world management plugin to create world's using the generators.

Available generators are:

```
void
candyland
winterwonder
pirateislands
```

### Using the Worlds plugin

You can download [Worlds by surva here](https://plugins.surva.net/#worlds). Worlds has first-class support for FancyGenerators and is our recommended world management plugin.

Create a new world using: `/worlds create <worldname> <generator name>`

E.g., to create a void world named void123, use `/worlds create void123 void`.

[Ask questions on Discord 💬](https://discord.gg/t4Kg4j3829)

## 🙋‍ Contribution
Feel free to contribute if you have ideas or found an issue.

You can:
- [open an issue](https://github.com/survanetwork/FancyGenerators/issues) (problems, bugs or feature requests)
- [create a pull request](https://github.com/survanetwork/FancyGenerators/pulls) (code contributions like fixed bugs or added features)

Please read our **[Contribution Guidelines](CONTRIBUTING.md)** before creating an issue or submitting a pull request.

Many thanks for their support to all contributors!

## 👨‍⚖️ License
[MIT](https://github.com/survanetwork/FancyGenerators/blob/master/LICENSE)
