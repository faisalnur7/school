---

**CONTEXT**

Laravel school asset management system. Existing models:

```php
// AssetCategory: id, name, description, is_active
// Asset: id, asset_category_id, name, description, quantity, purchase_price, current_value, status
```

---

**ADD THESE FEATURES**

**Departments** — `id, name, code (unique), description, is_active`

**Buildings** — `id, name, code (unique), description, is_active`

**Rooms** — `id, building_id, department_id (nullable), name, code (unique), floor_number, room_type (enum: classroom, lab, office, library, gymnasium, storage, staffroom, other), seating_capacity (nullable), is_active`

**Update Asset** — add nullable `room_id` FK

---

**RULES**

- `seating_capacity` on Room is source of truth — never derive from asset quantities
- FK style: `foreignId()->constrained()->cascadeOnDelete()`
- PHP enum for `room_type`
- Migrations in dependency order

---

**DELIVER IN ORDER**

1. Migrations
2. Models + updated `Asset` model
3. Seed JSON — 8 departments, 4 buildings, 20 rooms
4. `DatabaseSeeder` seeding order snippet

---