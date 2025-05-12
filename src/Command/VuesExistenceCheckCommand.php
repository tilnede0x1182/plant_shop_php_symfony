<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

#[AsCommand(
    name: 'app:vues:check',
    description: 'Liste les fichiers twig vides et non vides',
)]
class VuesExistenceCheckCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $finder = new Finder();
        $finder->files()->in('templates')->name('*.twig');

        $fichiersVides = [];
        $fichiersNonVides = [];

        foreach ($finder as $file) {
            if ($file->getSize() === 0) {
                $fichiersVides[] = $file->getRelativePathname();
            } else {
                $fichiersNonVides[] = $file->getRelativePathname();
            }
        }

        $io->section('Fichiers vides :');
        if (empty($fichiersVides)) {
            $io->success('Aucun fichier vide.');
        } else {
            foreach ($fichiersVides as $file) {
                $io->writeln('- ' . $file);
            }
        }

        $io->section('Fichiers non vides :');
        if (empty($fichiersNonVides)) {
            $io->warning('Aucun fichier non vide.');
        } else {
            foreach ($fichiersNonVides as $file) {
                $io->writeln('- ' . $file);
            }
        }

        return Command::SUCCESS;
    }
}
