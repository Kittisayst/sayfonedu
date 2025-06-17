=============================[filament]===============================

#ສ້າງ ຂໍ້ມູນ CRUD
php artisan make:filament-resource [ResourceName]
# ຄຳສັ່ງເສີມ --generate --view ໂດຍອ້າງອີງຈາກ model ສ້າງ From ແລະ ຕາຕະລາງ
php artisan make:filament-resource Schedule --generate --view
#ສ້າງ ຄວາມສຳພັນ
php artisan make:filament-relation-manager [ResourceName] [RelationshipName] [RelationshipTableName]

=============================[laravel]===================================
php artisan db:seed --class=UserSeeder

RelationManagers\GradesRelationManager::class,
RelationManagers\AttendanceRecordsRelationManager::class,