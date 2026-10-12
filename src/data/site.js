export const SITE_URL = 'https://www.sedivakstavby.cz';

export const contact = {
  phone: '731 115 020',
  phoneHref: 'tel:+420731115020',
  email: 'info@sedivakstavby.cz',
  invoiceEmail: 'faktury@sedivakstavby.cz',
  address: 'Dolní náměstí 46, 330 33 Město Touškov',
  hours: 'Po–Pá 7:00–17:00',
};

export const ELEKTRO_URL = 'https://www.sedivakelektro.cz/';
export const FACEBOOK_URL = 'https://www.facebook.com/profile.php?id=61566394221859';

// Fotky realizací – prvních šest je zároveň náhled na hlavní stránce.
export const PREVIEW = [6, 55, 43, 63, 7, 47];

// Pořadí zbytku galerie: nejdřív hotová práce, syrové stěny až na konec.
const ORDER = [
  85, 56, 44, 87, 49, 64, 62, 86, 45, 88, 57, 66,
  50, 52, 83, 77, 89, 75, 76, 65, 90, 58, 59, 60,
  53, 48, 46, 54, 51, 61, 67, 68, 69, 70, 71, 72,
  73, 74, 78, 79, 80, 81, 82, 84, 14, 8, 5, 1,
  3, 9, 10, 4, 15, 16, 17, 18, 19, 20, 2, 11,
  12, 13, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30,
  31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42,
];

export const PHOTOS = [...PREVIEW, ...ORDER.filter((n) => !PREVIEW.includes(n))];

const name = (n) => `foto-${String(n).padStart(2, '0')}`;

// Varianty 400/800 má každá fotka, 1600 jen část – proto jen dvě v srcset.
export const photo = (n) => ({
  src: `/images/gallery/r/${name(n)}-800.webp`,
  srcset: `/images/gallery/r/${name(n)}-400.webp 400w, /images/gallery/r/${name(n)}-800.webp 800w`,
  full: `/images/gallery/r/${name(n)}-full.webp`,
});
