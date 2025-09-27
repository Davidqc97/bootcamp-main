# 🧪 QA Report – Proyecto en InfinityFree

## 📋 Pruebas realizadas

### 1. Validación de formularios
- Caso: Campo email vacío  
- Resultado esperado: mensaje de error, debe completar este campo. 
- Resultado obtenido:

 ![Descripción](./images/11image.PNG)

---

- Caso: Email inválido (ej: "abc123")  
- Resultado esperado: no se guarda en BD, verifica que las credenciales son invalidas 
- Resultado obtenido: 

Al iniciar sesión con credenciales incorrectas

 ![Descripción](./images/121image.PNG)

 Se generá el siguiente mensaje de alerta indicando que las credenciales son invalidas. 

  ![Descripción](./images/122image.PNG)

---

### 2. Seguridad
- Caso: Intento de inyección SQL (`' OR 1=1 --`)  
- Resultado esperado: no insertar ni romper consulta  
- Resultado obtenido: 

 ![Descripción](./images/21image.PNG)


- Caso: Contraseña almacenada en BD  
- Resultado esperado: hash en vez de texto plano  
- Resultado obtenido: 

 ![Descripción](./images/22image.jpeg)

---

### 3. Funcionalidad
- Caso: Insertar registro válido  
- Resultado esperado: se guarda en la BD y aparece en `listar.php`  
- Resultado obtenido: 

 ![Descripción](./images/31image.PNG)

 ![Descripción](./images/32image.PNG)

- Caso: Exportar CSV  
- Resultado esperado: descarga archivo con registros  
- Resultado obtenido: 

 ![Descripción](./images/11image.PNG)

---

### 4. Login (si aplica)
- Caso: Usuario/contraseña correcta  
- Resultado esperado: acceso permitido  
- Resultado obtenido: 

 ![Descripción](./images/31image.PNG)

  ![Descripción](./images/33image.PNG)

- Caso: Usuario/contraseña incorrecta  
- Resultado esperado: acceso denegado  
- Resultado obtenido: 

 ![Descripción](./images/11image.PNG)

---

## ✅ Conclusiones QA
- [Breve conclusión del tester: ¿funciona correctamente? ¿qué falta por corregir?]

La aplicación presenta un comportamiento seguro y funcional en las pruebas realizadas:
- ✅ Validaciones de formulario funcionan correctamente
- ✅ Sistema resistente a inyecciones SQL básicas  
- ✅ Autenticación funciona según lo esperado


**Estado general**: ACEPTABLE para producción POC con funcionalidades básicas. 
**Recomendaciones**: Realizar pruebas adicionales con mayor volumen de datos.