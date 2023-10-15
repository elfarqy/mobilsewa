##Cara instalasi


## Menggunakan docker. 
- Install `docker-compose`
- Masuk ke directory utama.

```
cd ROOT_DIRECTORY
```
- run 
```
docker-compose up --build 
```

- untuk halaman utama ada di 

```
http://localhost:8081/backend/web/site/index
```
untuk api ada di 

```
http://localhost:8081/backend/web/api
```

## Versi manual


PHP : php 8.1 (lebih lengkap cek di directory php/dockerfile untuk extensi yang digunakan.)

mysql: versi 8.0 

### instalasi 
Masuk directory utama.

```cd ${root_url}```

Perform composer install 
```composer install```

Execute command 
```
php init --env=Production --overwrite=All
php yii migrate --interactive=0
php yii generator/data
```
### default akun 
Secara default untuk admin, 
`admin@example.com`. pass `123456`

Untuk user lain: 
- manager, execute command 
`select * from user where role = 'manager'`

- driver, execute command 
`select * from user where role = 'manager'`