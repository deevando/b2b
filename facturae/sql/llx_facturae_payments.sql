-- ===================================================================
-- Copyright (C) 2013 Ferran Marcet <fmarcet@2byte.es>
--
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 2 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program; if not, write to the Free Software
-- Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
--
-- ===================================================================

CREATE TABLE llx_facturae_payments
(
  rowid 			integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  entity			integer(11) NOT NULL DEFAULT 1,
  fk_payment		integer(11)	DEFAULT 0,
  facturae_cod		varchar(4) NOT NULL
  ) ENGINE=InnoDB;