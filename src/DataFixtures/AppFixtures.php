<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\User;
use App\Entity\Song;
use App\Entity\Playlist;
use App\Entity\Label;
use App\Entity\ArtistHasLabel;
use App\Entity\PlaylistHasSong;

use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 8; $i++) {
            
            //User
            $user = new User();
            $user->setFirstName("User_".$i);
            $user->setLastName("User_".$i);
            $user->setSexe("User_".rand(0,999));
            $user->setBirthDate(new DateTimeImmutable());
            $user->setEmail("User_".$i);
            $user->setIdUser("User_". $i);
            $user->setCreateAt(new DateTimeImmutable());
            $user->setUpdateAt(new DateTimeImmutable()); 
            $user->setPassword("$2y$".rand(0,999999999999999999));
            $manager->persist($user);
            $manager->flush();
                
            //Artist
            $artist = new Artist();
            $artist->setFullName("Artist_". $i);
            $artist->setCreateAt(new DateTimeImmutable());
            $artist->setUpdateAt(new DateTimeImmutable());
            $artist->setUserIdUser($user);
            $manager->persist($artist);
            $manager->flush();

            //Label
            $label = new Label();
            $label->setName("Label_". $i);
            $label->setYearCreation(new DateTimeImmutable());
            $label->setIdLabel($user);
            $manager->persist($label);
            $manager->flush();

            //Album
            $album = new Album();
            $album->setName("Album_". $i);
            $album->setCategory("Album_". $i);           
            $album->setCover("Album_". $i);
            $album->setYear(new DateTimeImmutable());
            $album->setArtistUserIdUser($artist);
            $album->setIdAlbum($user);
            $manager->persist($album);
            $manager->flush();

            //Song
            $song = new Song();
            $song->setTitle("Song_". $i);
            $song->setUrl("Song_". $i);
            $song->setCover("Song_". $i);
            $song->setVisibility(rand (0, 1));
            $song->setCreateAt(new DateTimeImmutable());
            $song->setIdSong($user);
            $song->setAlbum($album);
            $manager->persist($song);
            $manager->flush();   

            //Playlist
            $playlist = new Playlist();
            $playlist->setTitle("Playlist_". $i);
            $playlist->setPublic(rand(0,1));
            $playlist->setCreateAt(new DateTimeImmutable());
            $playlist->setUpdateAt(new DateTimeImmutable());
            $playlist->setIdPlaylist($user);
            $manager->persist($playlist);
            $manager->flush();

            //ArtistHasLabel
            $artisthaslabel = new ArtistHasLabel();
            $artisthaslabel->setCreateYear(new DateTimeImmutable());
            $artisthaslabel->setDeleteYear(new DateTimeImmutable());
            $manager->persist($artisthaslabel);
            $manager->flush(); 

            //ArtistHasSong
            $playlisthassong = new PlaylistHasSong();
            $playlisthassong->setDownload((rand(0,1)));
            $playlisthassong->setPosition("PlaylistHasSong_". $i);
            $playlisthassong->setCreateAt(new DateTimeImmutable());
            $manager->persist($playlisthassong);
            $manager->flush();
        

        }
    }
}