-- Update car statuses to original stage names
ALTER TABLE cars MODIFY COLUMN status VARCHAR(50);

UPDATE cars SET status = 'Intake' WHERE status = 'intake';
UPDATE cars SET status = 'Technische controle' WHERE status = 'technical';
UPDATE cars SET status = 'Verkoop klaar' WHERE status = 'ready_for_sale';

ALTER TABLE cars MODIFY COLUMN status ENUM('Intake','Technische controle','Herstel & Onderhoud','Commercieel gereed','Verkoop klaar','test_drive','sold');
