/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     16/1/2018 16:10:36                           */
/*==============================================================*/


drop table if exists EMPLEADO;

drop table if exists USUARIO;

/*==============================================================*/
/* Table: EMPLEADO                                              */
/*==============================================================*/
create table EMPLEADO
(
   ID_EMP               int not null auto_increment,
   NOMBRE_EMP           varchar(50),
   RUC_EMP              varchar(50),
   primary key (ID_EMP)
);

/*==============================================================*/
/* Table: USUARIO                                               */
/*==============================================================*/
create table USUARIO
(
   ID_EMP               int not null,
   ID_USU               numeric(50,0) not null,
   NOMBRE_USU           varchar(50),
   CEDULA_USU           varchar(50),
   GENERO_USU           varchar(50),
   primary key (ID_EMP, ID_USU)
);

alter table USUARIO add constraint FK_EMPLEADO_USUARIO foreign key (ID_EMP)
      references EMPLEADO (ID_EMP) on delete restrict on update restrict;

