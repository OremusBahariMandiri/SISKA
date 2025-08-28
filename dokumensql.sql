CREATE TABLE B02DokKontrak (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    IdKode VARCHAR(20),
    IdKodeA04 VARCHAR(20),
    NoRegDok VARCHAR(50) NOT NULL,
    KategoriDok VARCHAR(50) NOT NULL,
    JenisDok VARCHAR(50) NOT NULL,
    KetDok VARCHAR(255),
    ValidasiDok VARCHAR(20) NOT NULL,
    TglTerbitDok DATE NOT NULL,
    TglBerakhirDok DATE NOT NULL,
    MasaBerlaku VARCHAR(100),
    TglPengingat DATE,
    MasaPengingat VARCHAR(100),
    FileDok VARCHAR(255) NOT NULL,
    StatusDok VARCHAR(20) NOT NULL,
    created_by VARCHAR(50),
    updated_by VARCHAR(50),
    created_at VARCHAR(50),
    updated_at VARCHAR(50),
    FOREIGN KEY (IdKodeA04) REFERENCES A04DmKaryawan(IdKode),
    FOREIGN KEY (created_by) REFERENCES A01DmUser(IdKode),
    FOREIGN KEY (updated_by) REFERENCES A01DmUser(IdKode)
);

CREATE TABLE B03DokKarir (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    IdKode VARCHAR(20),
    IdKodeA04 VARCHAR(20),
    NoRegDok VARCHAR(50) NOT NULL,
    KategoriDok VARCHAR(50) NOT NULL,
    JenisDok VARCHAR(50) NOT NULL,
    KetDok VARCHAR(255),
    ValidasiDok VARCHAR(20) NOT NULL,
    TglTerbitDok DATE NOT NULL,
    TglBerakhirDok DATE NOT NULL,
    MasaBerlaku VARCHAR(100),
    TglPengingat DATE,
    MasaPengingat VARCHAR(100),
    FileDok VARCHAR(255) NOT NULL,
    StatusDok VARCHAR(20) NOT NULL,
    created_by VARCHAR(50),
    updated_by VARCHAR(50),
    created_at VARCHAR(50),
    updated_at VARCHAR(50),
    FOREIGN KEY (IdKodeA04) REFERENCES A04DmKaryawan(IdKode),
    FOREIGN KEY (created_by) REFERENCES A01DmUser(IdKode),
    FOREIGN KEY (updated_by) REFERENCES A01DmUser(IdKode)
);

CREATE TABLE B04DokLegalitas (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    IdKode VARCHAR(20),
    IdKodeA04 VARCHAR(20),
    NoRegDok VARCHAR(50) NOT NULL,
    KategoriDok VARCHAR(50) NOT NULL,
    JenisDok VARCHAR(50) NOT NULL,
    KetDok VARCHAR(255),
    ValidasiDok VARCHAR(20) NOT NULL,
    TglTerbitDok DATE NOT NULL,
    TglBerakhirDok DATE NOT NULL,
    MasaBerlaku VARCHAR(100),
    TglPengingat DATE,
    MasaPengingat VARCHAR(100),
    FileDok VARCHAR(255) NOT NULL,
    StatusDok VARCHAR(20) NOT NULL,
    created_by VARCHAR(50),
    updated_by VARCHAR(50),
    created_at VARCHAR(50),
    updated_at VARCHAR(50),
    FOREIGN KEY (IdKodeA04) REFERENCES A04DmKaryawan(IdKode),
    FOREIGN KEY (created_by) REFERENCES A01DmUser(IdKode),
    FOREIGN KEY (updated_by) REFERENCES A01DmUser(IdKode)
);

CREATE TABLE A08DmJabatan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    IdKode VARCHAR(20),
    GolonganJbt VARCHAR(100),
    Jabatan VARCHAR(100),
    SingkatanJbtn VARCHAR(100),
    created_by VARCHAR(50),
    updated_by VARCHAR(50),
    created_at VARCHAR(50),
    updated_at VARCHAR(50),
    FOREIGN KEY (created_by) REFERENCES A01DmUser(IdKode),
    FOREIGN KEY (updated_by) REFERENCES A01DmUser(IdKode)
)

CREATE TABLE A09DmDepartemen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    IdKode VARCHAR(20),
    GolonganDep VARCHAR(100),
    Departemen VARCHAR(100),
    SingkatanDep VARCHAR(100),
    created_by VARCHAR(50),
    updated_by VARCHAR(50),
    created_at VARCHAR(50),
    updated_at VARCHAR(50),
    FOREIGN KEY (created_by) REFERENCES A01DmUser(IdKode),
    FOREIGN KEY (updated_by) REFERENCES A01DmUser(IdKode)
)

CREATE TABLE A10DmWilayahKrj (
    id INT AUTO_INCREMENT PRIMARY KEY,
    IdKode VARCHAR(20),
    GolonganWilker VARCHAR(100),
    WilayahKerja VARCHAR(100),
    SingkatanWilker VARCHAR(100),
    created_by VARCHAR(50),
    updated_by VARCHAR(50),
    created_at VARCHAR(50),
    updated_at VARCHAR(50),
    FOREIGN KEY (created_by) REFERENCES A01DmUser(IdKode),
    FOREIGN KEY (updated_by) REFERENCES A01DmUser(IdKode)
)

ALTER TABLE B02DokKontrak
ADD NamaPrsh VARCHAR (20),
ADD CONSTRAINT fk_dokkontrak_perusahaan
FOREIGN KEY (NamaPrsh) REFERENCES A03DmPerusahaan(IdKode);

ALTER TABLE A06DmKategoriDok
ADD GolDok VARCHAR (200)

ALTER TABLE A07DmJenisDok
ADD GolDok VARCHAR (200)

ALTER TABLE B03DokKarir
ADD IdKodeA08 VARCHAR(20),
ADD IdKodeA09 VARCHAR(20),
ADD IdKodeA10 VARCHAR(20);

ALTER TABLE B03DokKarir
ADD IdKodeA08 VARCHAR (20),
ADD IdKodeA09 VARCHAR (20),
ADD IdKodeA10 VARCHAR (20),

ALTER TABLE A08DmJabatan ADD UNIQUE INDEX idx_idkode (IdKode);
ALTER TABLE A09DmDepartemen ADD UNIQUE INDEX idx_idkode (IdKode);
ALTER TABLE A10DmWilayahKrj ADD UNIQUE INDEX idx_idkode (IdKode);

ALTER TABLE B03DokKarir
ADD CONSTRAINT fk_dokkar_jab
FOREIGN KEY (IdKodeA08) REFERENCES A08DmJabatan(IdKode);

ALTER TABLE B03DokKarir
ADD CONSTRAINT fk_dokkar_dept
FOREIGN KEY (IdKodeA09) REFERENCES A09DmDepartemen(IdKode);

ALTER TABLE B03DokKarir
ADD CONSTRAINT fk_dokkar_wilker
FOREIGN KEY (IdKodeA10) REFERENCES A10DmWilayahKrj(IdKode);


ALTER TABLE A04DmKaryawan
ADD Catatan VARCHAR (200)

CREATE TABLE A11DmFormulirDok (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    IdKode VARCHAR(20),
    NoRegDok VARCHAR(50) NOT NULL,
    KategoriDok VARCHAR(50) NOT NULL,
    JenisDok VARCHAR(50) NOT NULL,
    KetDok VARCHAR(255),
    TglTerbitDok DATE NOT NULL,
    FileDok VARCHAR(255) NOT NULL,
    StatusDok VARCHAR(20) NOT NULL,
    created_by VARCHAR(50),
    updated_by VARCHAR(50),
    created_at VARCHAR(50),
    updated_at VARCHAR(50),
    FOREIGN KEY (created_by) REFERENCES A01DmUser(IdKode),
    FOREIGN KEY (updated_by) REFERENCES A01DmUser(IdKode)
);

ALTER TABLE A11DmFormulirDok
ADD NamaPrsh VARCHAR (20),
ADD CONSTRAINT fk_formulirdok_perusahaan
FOREIGN KEY (NamaPrsh) REFERENCES A03DmPerusahaan(IdKode);

CREATE TABLE B06DokBpisKes (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    IdKode VARCHAR(20) NOT NULL,
    IdKodeA04 VARCHAR(20),
    NoRegDok VARCHAR(50) NOT NULL,
    KategoriDok VARCHAR(50) NOT NULL,
    JenisDok VARCHAR(50) NOT NULL,
    TglTerbitDok DATE NOT NULL,
    TglBerakhirDok DATE NOT NULL,
    MasaBerlaku VARCHAR(100),
    KetDok TEXT,
    UpahKtrKry DECIMAL(15,2),
    UpahBrshKry DECIMAL(15,2),
    IuranPrshPersen DECIMAL(5,2),
    IuranPrshRp DECIMAL(15,2),
    IuranKryPersen DECIMAL(5,2),
    IuranKryRp DECIMAL(15,2),
    IuranKry1Rp DECIMAL(15,2),
    IuranKry2Rp DECIMAL(15,2),
    IuranKry3Rp DECIMAL(15,2),
    JmlPrshRp DECIMAL(15,2),
    JmlKryRp DECIMAL(15,2),
    TotIuran DECIMAL(15,2),
    FileDok VARCHAR(255),
    StatusDok VARCHAR(20) NOT NULL,
    created_by VARCHAR(50),
    updated_by VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (IdKodeA04) REFERENCES A04DmKaryawan(IdKode),
    FOREIGN KEY (created_by) REFERENCES A01DmUser(IdKode),
    FOREIGN KEY (updated_by) REFERENCES A01DmUser(IdKode)
);