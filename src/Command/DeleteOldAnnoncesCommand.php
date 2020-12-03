<?php

namespace App\Command;

use PDO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteOldAnnoncesCommand extends Command
{
    private EntityManagerInterface $entitymanager;

    private $mailer;

    private SymfonyStyle $io;

    public function __construct(EntityManagerInterface $entitymanager, MailerInterface $mailer)
    {
        parent::__construct();
        $this->entitymanager = $entitymanager;
        $this->mailer = $mailer;
    }
    
    protected static $defaultName = 'app:delete-old-annonces';

    protected function configure()
    {
        $this->setDescription('Supprime les annonces qui on plus de 15 jours');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->deleteOldAnnonces();

        return Command::SUCCESS;
    }

    private function deleteOldAnnonces()
    {
        $this->io->section("SUPPRESSION DES ANNONCES QUI ONT PLUS DE 15 JOURS EN BASE DE DONNEES");

        $sql= "SELECT u.email, a.users_id, a.title FROM annonces a
        join users u on u.id = a.users_id
        where CURRENT_DATE >= created_at  
        and created_at <= DATE_ADD(CURRENT_DATE(), INTERVAL -15 DAY)
        and active = true";

        $databaseConnection = $this->entitymanager->getConnection();

        $statement = $databaseConnection->prepare($sql);
        $statement->execute();

        $annonces = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($annonces as $annonce){
            $mail =  $annonce['email'];
            $title = $annonce['title'];
            $email = (new TemplatedEmail())
            ->from('no-reply@petitesannonces.test')
            ->to($mail)
            ->subject('Annonce depuis le site Récif Annonces')
            ->htmlTemplate('emails/delete_old_annonce.html.twig')
            ->context([
                'mail' => $mail,
                'title' => $title,
                'sujet' => ('Suppression d\'annonce'),
            ])
        ;

        $this->mailer->send($email);
    }
    $sql = "SELECT * FROM annonces 
    WHERE CURRENT_DATE >= created_at  
    and created_at <= DATE_ADD(CURRENT_DATE(), INTERVAL -15 DAY) 
    AND active = true";

    $statement = $databaseConnection->query($sql);
    $annoncesDeleted = $statement->rowCount();
    if ($annoncesDeleted >= 1) {
        $message = "{$annoncesDeleted} ANNONCES ONT ETE SUPPRIMER DE LA BASE DE DONNEES";
    }else{
        $message = "AUCUNES ANNONCES ONT ETE SUPPRIMER DE LA BASE DE DONNEES";
    }
    $this->io->success($message);
    }
}
