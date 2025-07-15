-- Creación de tablas

CREATE TABLE Categoria (
    ID_Categoria NUMBER CONSTRAINT Categoria_PK PRIMARY KEY,
    Nombre_Categoria VARCHAR2(100),
    ID_Estado NUMBER,
    FOREIGN KEY (ID_Estado) REFERENCES FIDE_ESTADOS_TB(ID_ESTADO)
);
/

CREATE TABLE Unidad_Medida (
    ID_Unidad_Medida NUMBER CONSTRAINT Unidad_Medida_PK PRIMARY KEY,
    Nombre_Unidad_Medida VARCHAR2(100),
    ID_Estado NUMBER,
    FOREIGN KEY (ID_Estado) REFERENCES FIDE_ESTADOS_TB(ID_ESTADO)
);
/

CREATE TABLE Producto (
    ID_Producto NUMBER CONSTRAINT Producto_PK PRIMARY KEY,
    Nombre VARCHAR2(100),
    ID_Categoria NUMBER,
    ID_Unidad_Medida NUMBER,
    ID_Estado NUMBER,
    FOREIGN KEY (ID_Categoria) REFERENCES Categoria(ID_Categoria),
    FOREIGN KEY (ID_Unidad_Medida) REFERENCES Unidad_Medida(ID_Unidad_Medida),
    FOREIGN KEY (ID_Estado) REFERENCES FIDE_ESTADOS_TB(ID_ESTADO)
);
/

CREATE TABLE Inventario (
    ID_Inventario NUMBER CONSTRAINT Inventario_PK PRIMARY KEY,
    Cantidad NUMBER,
    Fecha_Ingreso DATE,
    ID_Producto NUMBER,
    ID_Estado NUMBER,
    FOREIGN KEY (ID_Producto) REFERENCES Producto(ID_Producto),
    FOREIGN KEY (ID_Estado) REFERENCES FIDE_ESTADOS_TB(ID_ESTADO)
);
/

