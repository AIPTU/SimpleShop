# SimpleShop

A powerful and user-friendly shop plugin for PocketMine-MP servers that allows players to buy and sell items through an intuitive form-based interface.

## Features

- **Intuitive Form Interface**: Easy-to-use forms for both players and administrators
- **Category & Sub-Category System**: Organize items in hierarchical categories
- **Flexible Economy Support**: Compatible with BedrockEconomy and XP-based economies
- **Permission-Based Access**: Control who can access specific categories and features
- **Hidden Categories**: Create admin-only or special permission categories
- **Item Management**: Add, edit, and remove items with full NBT support
- **Buy & Sell System**: Players can both purchase and sell items
- **Image Support**: Custom images for categories and items (URL or path-based)
- **Priority System**: Control the order of categories and items
- **Pagination**: Automatic pagination for large inventories

## Requirements

- PocketMine-MP 5.30.0+
- PHP 8.1+
- Required Virions:
  - [Commando](https://github.com/CortexPE/Commando) - Command framework
  - [libPiggyEconomy](https://github.com/DaPigGuy/libPiggyEconomy) - Economy provider
  - [PocketForm](https://github.com/XanderID/PocketForm) - Form library

## Configuration

### config.yml

```yaml
# Economy settings.
# Possible providers: bedrockeconomy, xp
economy:
  provider: xp
```

**Economy Providers:**
- `BedrockEconomy`: Uses BedrockEconomy plugin for money transactions
- `xp`: Uses player experience points as currency

## Commands

| Command | Permission | Description |
|---------|------------|-------------|
| `/shop` | `simpleshop.command.shop.access` | Open the main shop interface |
| `/shop <category>` | `simpleshop.command.shop.access` | Open a specific category directly |
| `/shopadmin` | `simpleshop.command.shop.admin` | Access the admin panel |

## Permissions

### Default Permissions

| Permission | Default | Description |
|------------|---------|-------------|
| `simpleshop.category` | `op` | Allows usage of all SimpleShop categories |
| `simpleshop.command.shop.access` | `true` | Allows players to use the shop command |
| `simpleshop.command.shop.admin` | `op` | Allows access to admin commands |

### Dynamic Permissions

The plugin automatically creates permissions for each category and sub-category:

- **Categories**: `simpleshop.category.<category_id>`
- **Sub-categories**: `simpleshop.subcategory.<parent_id>.<subcategory_id>`

## Usage

### For Players

1. **Opening the Shop**: Use `/shop` to open the main shop interface
2. **Browsing Categories**: Click on categories to view their contents
3. **Viewing Items**: Click on items to see details, prices, and enchantments
4. **Buying Items**: 
   - Click "Buy" on an item
   - Select quantity using the slider
   - Confirm the purchase
5. **Selling Items**:
   - Hold the item you want to sell
   - Click "Sell" on the matching shop item
   - Select quantity and confirm

### For Administrators

#### Adding Categories

1. Use `/shopadmin` to open the admin panel
2. Click "Add New Category"
3. Fill in the form:
   - **Category Name**: Display name for the category
   - **Description**: Brief description (optional)
   - **Priority**: Sort order (0 = default)
   - **Image Source**: URL or file path for category icon
   - **Image Type**: URL or Path
   - **Hidden**: Hide from regular players
   - **Permission**: Custom permission (auto-generated if empty)

#### Adding Sub-Categories

1. Open a category in the admin panel
2. Click "Add New Sub-Category"
3. Fill in similar details as categories

#### Adding Items

1. **Hold the item** you want to add in your hand
2. Navigate to the desired category or sub-category
3. Click "Add New Item"
4. Configure:
   - **Buy Price**: How much players pay to buy
   - **Can Buy**: Enable/disable buying
   - **Sell Price**: How much players receive when selling
   - **Can Sell**: Enable/disable selling
   - **Image Source**: Custom item icon (optional)
   - **Image Type**: URL or Path

#### Editing Items

Items can be edited in two ways:
1. **Update with Held Item**: Updates NBT data from the item in your hand
2. **Edit Properties Manually**: Only changes prices and settings

## API Usage

### Getting the Shop Manager

```php
use aiptu\simpleshop\SimpleShop;

$shopManager = SimpleShop::getInstance()->getShopManager();
```

### Adding Categories Programmatically

```php
use aiptu\simpleshop\shops\ShopCategory;
use aiptu\simpleshop\utils\ImageType;

$category = new ShopCategory(
    'tools',                    // ID
    'Tools',                    // Name
    'Various tools and items',  // Description
    0,                          // Priority
    'textures/items/tools',     // Image source
    ImageType::PATH,            // Image type
    'simpleshop.category.tools', // Permission
    false                       // Hidden
);

$shopManager->addCategory($category);
```

## Data Format

### Category Structure

```json
{
  "category_id": {
    "name": "Category Name",
    "description": "Category description",
    "priority": 0,
    "image_source": "path/to/image",
    "image_type": "path",
    "hidden": false,
    "permission": "simpleshop.category.category_id",
    "items": {},
    "sub_categories": {}
  }
}
```

### Item Structure

```json
{
  "item_id": {
    "nbt": "base64_encoded_nbt_data",
    "buy": 10.0,
    "sell": 5.0,
    "can_buy": true,
    "can_sell": true,
    "image_source": "",
    "image_type": "path"
  }
}
```

## Troubleshooting

### Common Issues

1. **"Missing virion" errors**: Ensure all required virions are properly installed
2. **Economy provider not found**: Check that your economy plugin is installed and the provider name is correct
3. **Permissions not working**: Verify permission setup and check that categories aren't hidden
4. **Items not deserializing**: This usually indicates corrupted NBT data in shops.json

## Additional Notes

- If you find bugs or want to give suggestions, please visit [here](https://github.com/AIPTU/SimpleShop/issues).
- We accept all contributions! If you want to contribute, please make a pull request in [here](https://github.com/AIPTU/SimpleShop/pulls).
- Icons made from [www.flaticon.com](https://www.flaticon.com/free-animated-icons/groceries)
