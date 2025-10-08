#!/bin/bash

# =================================
# SCRIPT DE CONFIGURACIÓN DE RÉPLICA MYSQL
# =================================

echo "🔧 Configurando replicación MySQL Master-Slave..."

# Esperar a que el master esté listo
echo "⏳ Esperando que el master esté disponible..."
until docker exec taller-docker-web-db-master-1 mysqladmin ping -h localhost -u root -proot123 --silent; do
    sleep 2
done
echo "✅ Master está disponible"

# Crear usuario de replicación en el master
echo "👤 Creando usuario de replicación..."
docker exec taller-docker-web-db-master-1 mysql -u root -proot123 -e "
CREATE USER IF NOT EXISTS 'replica_user'@'%' IDENTIFIED BY 'replica_pass';
GRANT REPLICATION SLAVE ON *.* TO 'replica_user'@'%';
FLUSH PRIVILEGES;
"

# Obtener posición del master
echo "📍 Obteniendo posición del binary log del master..."
MASTER_STATUS=$(docker exec taller-docker-web-db-master-1 mysql -u root -proot123 -e "SHOW MASTER STATUS\G")
MASTER_FILE=$(echo "$MASTER_STATUS" | grep "File:" | awk '{print $2}')
MASTER_POSITION=$(echo "$MASTER_STATUS" | grep "Position:" | awk '{print $2}')

echo "📁 Master file: $MASTER_FILE"
echo "📍 Master position: $MASTER_POSITION"

# Configurar slaves
configure_slave() {
    local SLAVE_CONTAINER=$1
    local SLAVE_NAME=$2
    
    echo "⚙️ Configurando $SLAVE_NAME..."
    
    # Esperar a que el slave esté listo
    until docker exec $SLAVE_CONTAINER mysqladmin ping -h localhost -u root -proot123 --silent; do
        sleep 2
    done
    
    # Configurar replicación
    docker exec $SLAVE_CONTAINER mysql -u root -proot123 -e "
    STOP SLAVE;
    CHANGE MASTER TO
        MASTER_HOST='db-master',
        MASTER_USER='replica_user',
        MASTER_PASSWORD='replica_pass',
        MASTER_LOG_FILE='$MASTER_FILE',
        MASTER_LOG_POS=$MASTER_POSITION;
    START SLAVE;
    "
    
    echo "✅ $SLAVE_NAME configurado correctamente"
}

# Configurar ambos slaves
configure_slave "taller-docker-web-db-slave1-1" "Slave 1"
configure_slave "taller-docker-web-db-slave2-1" "Slave 2"

# Verificar estado de replicación
echo "🔍 Verificando estado de replicación..."
docker exec taller-docker-web-db-slave1-1 mysql -u root -proot123 -e "SHOW SLAVE STATUS\G" | grep -E "Slave_IO_Running|Slave_SQL_Running|Seconds_Behind_Master"

echo "🎉 ¡Configuración de replicación completada!"
echo ""
echo "📊 Para monitorear la replicación:"
echo "docker exec taller-docker-web-db-slave1-1 mysql -u root -proot123 -e 'SHOW SLAVE STATUS\G'"
echo ""
echo "🔗 Puertos de conexión:"
echo "  - Master:  localhost:3306"
echo "  - Slave 1: localhost:3307"
echo "  - Slave 2: localhost:3308"