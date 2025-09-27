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

 ![Descripción](./images/11image.PNG)

---

### 2. Seguridad
- Caso: Intento de inyección SQL (`' OR 1=1 --`)  
- Resultado esperado: no insertar ni romper consulta  
- Resultado obtenido: [Anotar aquí]

- Caso: Contraseña almacenada en BD  
- Resultado esperado: hash en vez de texto plano  
- Resultado obtenido: [Anotar aquí]

### 3. Funcionalidad
- Caso: Insertar registro válido  
- Resultado esperado: se guarda en la BD y aparece en `listar.php`  
- Resultado obtenido: [Anotar aquí]

- Caso: Exportar CSV  
- Resultado esperado: descarga archivo con registros  
- Resultado obtenido: [Anotar aquí]

### 4. Login (si aplica)
- Caso: Usuario/contraseña correcta  
- Resultado esperado: acceso permitido  
- Resultado obtenido: [Anotar aquí]

- Caso: Usuario/contraseña incorrecta  
- Resultado esperado: acceso denegado  
- Resultado obtenido: [Anotar aquí]

## ✅ Conclusiones QA
- [Breve conclusión del tester: ¿funciona correctamente? ¿qué falta por corregir?]