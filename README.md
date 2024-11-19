# ZuriORM

ZuriORM is a lightweight, easy-to-use Object-Relational Mapping (ORM) library for PHP applications. It simplifies database interactions by abstracting SQL queries and providing an intuitive API for developers. Whether you’re building a small project or a large-scale application, ZuriORM helps you focus on your logic without worrying about complex SQL syntax.

---

## **Features**
- **Simple Setup**: Connect to your database with just a few lines.
- **CRUD Operations**: Easily create, read, update, and delete records.
- **Query Builder**: Chain methods to build complex queries effortlessly.
- **Relationships**: Handle `hasOne` and `hasMany` relationships.
- **Transactions**: Safely execute database operations with support for transactions.
- **Soft Deletes**: Mark records as deleted without permanently removing them.
- **Aggregations**: Perform operations like `COUNT`, `SUM`, `AVG`, `MAX`, and `MIN`.
- **Pagination**: Simplify pagination with a single method.
- **Validation**: Validate data before saving to the database.
- **Custom Scopes**: Reuse predefined query conditions.

---

## **Installation**
1. Clone or download the repository.
2. Include the library in your project:
   ```php
   require_once 'path/to/ZuriORM.php';
   ```

3. Set up your database configuration in `config/database.php`:
   ```php
   return [
       'host' => 'localhost',
       'dbname' => 'your_database_name',
       'username' => 'your_username',
       'password' => 'your_password',
   ];
   ```

---

## **Basic Usage**
### **Initialize ZuriORM**
```php
use App\ZuriORM;

$orm = new ZuriORM();
```

---

### **Insert Data**
```php
$id = $orm->create('users', [
    'name' => 'Jonathan Odoh',
    'email' => 'jonathanodoh3140@gmail.com',
    'status' => 'active'
]);

echo "Inserted record ID: $id";
```

---

### **Read Data**
```php
// Fetch all active users
$users = $orm->read('users', ['status' => 'active']);

foreach ($users as $user) {
    echo $user['name'] . ' - ' . $user['email'] . PHP_EOL;
}
```

---

### **Update Data**
```php
$updatedRows = $orm->update('users', ['status' => 'inactive'], ['id' => 1]);

echo "$updatedRows record(s) updated.";
```

---

### **Delete Data**
```php
$deletedRows = $orm->delete('users', ['id' => 1]);

echo "$deletedRows record(s) deleted.";
```

---

### **Soft Delete**
```php
$softDeletedRows = $orm->softDelete('users', ['id' => 1]);

echo "$softDeletedRows record(s) marked as deleted.";
```

---

### **Chained Query Builder**
```php
$results = $orm->setTable('users')
               ->select(['name', 'email'])
               ->where('status', '=', 'active')
               ->orderBy('name', 'ASC')
               ->limit(10)
               ->execute();

foreach ($results as $user) {
    echo $user['name'] . ' - ' . $user['email'] . PHP_EOL;
}
```

---

### **Relationships**
#### **hasOne**
```php
$profile = $orm->setTable('users')->hasOne('Profile', 'user_id', 'id');

print_r($profile);
```

#### **hasMany**
```php
$posts = $orm->setTable('users')->hasMany('Post', 'user_id', 'id');

foreach ($posts as $post) {
    echo $post->title . PHP_EOL;
}
```

---

### **Aggregations**
```php
$totalUsers = $orm->setTable('users')->count('id');
echo "Total Users: $totalUsers";

$averageAge = $orm->setTable('users')->avg('age');
echo "Average Age: $averageAge";
```

---

### **Transactions**
```php
try {
    $orm->beginTransaction();

    $orm->create('accounts', ['user_id' => 1, 'balance' => 100]);
    $orm->create('transactions', ['account_id' => 1, 'amount' => -50]);

    $orm->commit();
    echo "Transaction completed successfully.";
} catch (Exception $e) {
    $orm->rollback();
    echo "Transaction failed: " . $e->getMessage();
}
```

---

### **Validation**
```php
$data = ['email' => 'invalid-email'];
$rules = ['email' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'];

try {
    $orm->validate($data, $rules);
} catch (Exception $e) {
    echo $e->getMessage();
}
```

---

### **Pagination**
```php
$users = $orm->setTable('users')->paginate(10, 2); // 10 per page, page 2

foreach ($users as $user) {
    echo $user['name'] . PHP_EOL;
}
```

---

## **Contribution**
We welcome contributions to make ZuriORM better! If you'd like to report bugs, suggest features, or contribute code:
1. Fork the repository.
2. Create a new branch (`feature/your-feature`).
3. Commit your changes.
4. Push to your fork.
5. Submit a pull request.

---

## **License**
This project is open-source and licensed under the [MIT License](LICENSE).

---

## **Acknowledgments**
ZuriORM is inspired by popular PHP frameworks like Laravel. Special thanks to all contributors and testers.

---

For questions or support, feel free to [open an issue](https://github.com/jona-odoh/zuriorm/issues).