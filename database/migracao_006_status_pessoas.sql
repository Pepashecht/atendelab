-- Migração Aula 006
-- A tabela `pessoas` original não possuía coluna de status, o que obrigava
-- o botão "Excluir" a apagar fisicamente o registro. Isso quebra a regra de
-- negócio da Aula 006 (Seção 12 do material): pessoas podem estar vinculadas
-- a atendimentos, então o histórico precisa ser preservado por inativação.
--
-- Execute este script UMA VEZ no banco `atendelab` (via phpMyAdmin ou linha de
-- comando) antes de testar o módulo de Pessoas integrado.

ALTER TABLE `pessoas`
  ADD COLUMN `status` ENUM('ativo','inativo') NOT NULL DEFAULT 'ativo' AFTER `cidade`;
