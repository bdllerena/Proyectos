/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     18/1/2018 09:37:40                           */
/*==============================================================*/


drop table if exists CARRERA;

drop table if exists DEPARTAMENTO;

drop table if exists EMPLEADO;

drop table if exists ESPECIALIDAD;

drop table if exists PERSONA;

/*==============================================================*/
/* Table: CARRERA                                               */
/*==============================================================*/
create table CARRERA
(
   COD_CAR              int not null auto_increment,
   NOM_CAR              varchar(120),
   EST_CAR              bool,
   primary key (COD_CAR)
)engine=InnoDB;

/*==============================================================*/
/* Table: DEPARTAMENTO                                          */
/*==============================================================*/
create table DEPARTAMENTO
(
   COD_DEP              int not null auto_increment,
   NOM_DEP              varchar(120),
   REP_DEP              varchar(250),
   primary key (COD_DEP)
)engine=InnoDB;

/*==============================================================*/
/* Table: EMPLEADO                                              */
/*==============================================================*/
create table EMPLEADO
(
   COD_EMP              int not null auto_increment,
   COD_PER              int not null,
   FIN_EMP              date,
   EST_EMP              bool,
   primary key (COD_EMP)
)engine=InnoDB;

/*==============================================================*/
/* Table: ESPECIALIDAD                                          */
/*==============================================================*/
create table ESPECIALIDAD
(
   COD_ESP              int not null auto_increment,
   COD_CAR              int not null,
   NOM_ESP              varchar(120),
   primary key (COD_ESP)
)engine=InnoDB;

/*==============================================================*/
/* Table: PERSONA                                               */
/*==============================================================*/
create table PERSONA
(
   COD_PER              int not null auto_increment,
   COD_DEP              int not null,
   APE_PER              varchar(40),
   NOM_PER              varchar(40),
   CED_PER              varchar(10),
   FEC_PER              date,
   primary key (COD_PER)
)engine=InnoDB;

alter table EMPLEADO add constraint FK_PERSONA_EMPLEADO foreign key (COD_PER)
      references PERSONA (COD_PER) on delete restrict on update restrict;

alter table ESPECIALIDAD add constraint FK_CARRERA_ESPECIALIDAD foreign key (COD_CAR)
      references CARRERA (COD_CAR) on delete restrict on update restrict;

alter table PERSONA add constraint FK_DEPARTAMENTO_PERSONA foreign key (COD_DEP)
      references DEPARTAMENTO (COD_DEP) on delete restrict on update restrict;

