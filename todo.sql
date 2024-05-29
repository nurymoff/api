-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema todowzcz0225
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema todowzcz0225
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `todowzcz0225` DEFAULT CHARACTER SET utf8 ;
USE `todowzcz0225` ;

-- -----------------------------------------------------
-- Table `todowzcz0225`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `todowzcz0225`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(32) NOT NULL,
  `password` VARCHAR(32) NOT NULL,
  `email` VARCHAR(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `username_UNIQUE` (`username` ASC),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `todowzcz0225`.`tasks`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `todowzcz0225`.`tasks` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `users_id` INT NOT NULL,
  `name` VARCHAR(32) NOT NULL,
  `description` TEXT NULL,
  `status` ENUM('todo', 'in_progress', 'done') NOT NULL DEFAULT 'todo',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_tasks_users_idx` (`users_id` ASC),
  CONSTRAINT `fk_tasks_users`
    FOREIGN KEY (`users_id`)
    REFERENCES `todowzcz0225`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
