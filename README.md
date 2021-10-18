![Open Source Love](https://img.shields.io/badge/open-source-lightgrey?style=for-the-badge&logo=github)
![](https://img.shields.io/badge/Magento-2.3.x-orange?style=for-the-badge&logo=magento)
![](https://img.shields.io/badge/Magento-2.4.x-orange?style=for-the-badge&logo=magento)
### Magento 2 DataPatchCreator

When working with large teams, many times someone forgets to create
a data patch for a cms page, block, configuration or a product attribute.\
This module allows you to create `PHP Data Patch Files` in Magento 2 Admin panel and export
them to `PHP` files which you can add to your code repositories.

### Installation

Installation is via `composer`
```
composer require enanobots/m2-datapatchcreator
```

After installing the packages just run:
```
php bin/magento setup:upgrade
```

### Requirements:
* `PHP 7.3` and higher
* `Magento 2.3.x` and higher

### Tested on:
* `Magento 2.3.x` OpenSource
* `Magento 2.4.x` OpenSource

#### Available Data Types for data patches:
* Product Attributes (with image swatches sync)
* CMS Pages
* CMS Blocks
* Store Configuration
* *more to come in future releases*

### How to create data patches?
You do everything in Magento 2 admin panel. :)

There are 2 ways to create Magento 2 Data Patch files:
* direct download (**DEFAULT OPTION**): 
  * for a single entity, a `PHP` file is generated
  * for sets of data patch files (Mass Exports), a `ZIP` file containing patch files is generated
* local files:
  * files are always created in specified location (both for single entities and mass exports)

### Sync images between store copies:

Module allows images synchronization between Magento 2 store copies.\
Class used to `dump / fetch` images needs to implement `ImageSyncInterface`
#### Default Image sync: `LocalFile`
* Files are copied to target location when patch file is created
* Files are copied from configured location to Magento 2 `media` folded 

### Adding new Image Sync Models:
Image Sync Models are passed into `array` of `ImageSync` class via `DI.XML`
```xml
    <type name="Nanobots\DataPatchCreator\Model\ImagesSync\ImageSync">
        <arguments>
            <argument name="syncModels" xsi:type="array">
                <item name="LocalFile" xsi:type="object">Nanobots\DataPatchCreator\Model\ImagesSync\LocalFile</item>
            </argument>
        </arguments>
    </type>
```
If you want to add a new `ImageSync` model, just add a new element to `syncModels` array via `di.xml` \
This key needs to match the store configuration set for
`sync_model` configuration value.

Create a plugin for `SyncMethods` and make sure the `value` field matches 
array key passed in `di.xml`.

For example:
```xml
    <type name="Nanobots\DataPatchCreator\Model\ImagesSync\ImageSync">
        <arguments>
            <argument name="syncModels" xsi:type="array">
                <item name="AmazonS3" xsi:type="object">YourVendor\YourModule\ImagesSync\AmazonS3</item>
            </argument>
        </arguments>
    </type>
```

```php  
        return [
            ['value' => "AmazonS3", 'label' => __('Copy images to designated folder')],
        ];
```
Setting configuration in the admin panel will make force usage of your module for images sync.