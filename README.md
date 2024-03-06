<h2>Opis</h2>
Jest bazwowy projekt Symfony postawiony na Dockerze, wykorzystywany jako szkielet aplikacji. Projekt korzysta z Symfony Generic Views.
To jest kompletny stos do uruchamiania Symfony 6.2 w kontenerach Docker za pomocą narzędzia docker-compose.

Składa się z 4 kontenerów:

<li>nginx, działający jako serwer WWW. </li>
<li>php, kontener PHP-FPM z wersją PHP 8.2. </li>
<li>db, który jest kontenerem bazy danych MySQL z obrazem MySQL 8.0.</li>

<h2>Instalacja</h2>
<li>Sklonuj to repozytorium.</li>

<li>Jeśli pracujesz z Docker Desktop dla systemu Mac, upewnij się, że włączyłeś VirtioFS dla implementacji udostępniania. VirtioFS zapewnia poprawioną wydajność we/wy dla operacji na bind mountach. Włączenie VirtioFS automatycznie włączy framework wirtualizacji.</li>

<li>Utwórz plik ./.docker/.env.nginx.local, korzystając z ./.docker/.env.nginx jako szablonu. Wartość zmiennej <code>NGINX_BACKEND_DOMAIN</code> to <code>server_name</code> używany w NGINX.</li>

<li>Przejdź do folderu ./docker i uruchom docker-compose up -d, aby uruchomić kontenery.</li>

<li>Wewnątrz kontenera php uruchom composer install, aby zainstalować zależności z folderu /var/www/symfony.</li>

<li>
Użyj następującej wartości dla zmiennej środowiskowej DATABASE_URL:
<code>DATABASE_URL=mysql://app_user:helloworld@db:3306/app_db?serverVersion=8.0.33</code>
</li>


<i>Powinieneś pracować wewnątrz kontenera php. Ten projekt jest skonfigurowany do pracy z rozszerzeniem Remote Container dla Visual Studio Code, więc możesz uruchomić polecenie Reopen in container po otwarciu projektu.</i>
<i>Możesz zmienić nazwę, użytkownika i hasło bazy danych w pliku env znajdującym się w głównym katalogu projektu.</i>
