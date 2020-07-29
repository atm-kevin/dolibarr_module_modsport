ALTER TABLE llx_modsport_sportactivities ADD fk_product INT(11) NOT NULL DEFAULT 0;
ALTER TABLE llx_modsport_sportactivities ADD CONSTRAINT fk_product FOREIGN KEY (fk_product) REFERENCES llx_product(rowid);
