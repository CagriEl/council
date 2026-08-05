import type { RouteSchedule } from '../types/schedule';
import type { InfrastructureWork } from '../types/infrastructure';
import type { NewsItem } from '../services/newsService';

export type { RouteSchedule };

export const mockAnnouncements: NewsItem[] = [
  {
    id: 1,
    title: 'Su Kesintisi Duyurusu',
    excerpt: 'Merkez ilçede planlı bakım çalışması nedeniyle su kesintisi yapılacaktır.',
    imageUrl: null,
    publishedAt: '2026-06-20T10:00:00+00:00',
    formattedDate: '20 Haziran 2026',
    isHeadline: true,
    categoryLabel: 'Genel duyuru',
    announcementType: 'duyuru',
  },
  {
    id: 2,
    title: 'Yaz Konserleri Başlıyor',
    excerpt: 'Kırklareli Belediyesi yaz konserleri 1 Temmuz\'da başlıyor.',
    imageUrl: null,
    publishedAt: '2026-06-18T10:00:00+00:00',
    formattedDate: '18 Haziran 2026',
    isHeadline: false,
    categoryLabel: 'Genel duyuru',
    announcementType: 'duyuru',
  },
  {
    id: 3,
    title: 'Meclis Toplantısı',
    excerpt: 'Haziran ayı olağan meclis toplantısı duyurusu.',
    imageUrl: null,
    publishedAt: '2026-06-15T10:00:00+00:00',
    formattedDate: '15 Haziran 2026',
    isHeadline: false,
    categoryLabel: 'Genel duyuru',
    announcementType: 'duyuru',
  },
];

export const mockQuickAccessItems = [
  { id: 'mayor', label: 'Başkan', icon: 'badge' as const, route: '/mudur' },
  { id: 'news', label: 'Haberler', icon: 'newspaper' as const, route: '/news' },
  { id: 'announcements', label: 'Duyurular', icon: 'campaign' as const, route: '/haberler' },
  { id: 'tenders', label: 'İhaleler', icon: 'account_balance' as const, route: '/ihaleler' },
  { id: 'request', label: 'Talep', icon: 'pending_actions' as const, route: '/requests/new' },
  { id: 'schedule', label: 'Saatler', icon: 'schedule' as const, route: '/saatler' },
  {
    id: 'infrastructure',
    label: 'Altyapı Çalışmaları',
    icon: 'construction' as const,
    route: '/infrastructure',
  },
  { id: 'directory', label: 'Rehber', icon: 'contact_page' as const, route: '/directory' },
  { id: 'members', label: 'Meclis Üyeleri', icon: 'groups' as const, route: '/meclis-uyeleri' },
  { id: 'council', label: 'Meclis Kararları', icon: 'gavel' as const, route: '/meclis-kararlari' },
];

export const mockSocialLinks = [
  { id: 'instagram', label: 'Instagram', url: 'https://www.instagram.com/kirklarelibelediyesi' },
];

export const mockMayor = {
  name: 'Derya Bulut',
  title: 'Kırklareli Belediye Başkanı',
  imageUrl: null as string | null,
  biography: `1968 yılında Kırklareli'de doğdu. İlk, orta ve lise öğrenimini Kırklareli'nde tamamladı. Üniversite eğitimini tamamladıktan sonra iş hayatına atıldı.

Siyasi kariyerine Milliyetçi Hareket Partisi'nde başladı. Parti içinde çeşitli görevler üstlendi ve yerel siyasette aktif rol aldı.

31 Mart 2024 Mahalli İdareler Genel Seçimleri'nde Kırklareli Belediye Başkanı seçildi.`,
  message: 'Kırklareli\'yi daha yaşanabilir, daha modern ve daha güçlü bir şehir haline getirmek için gece gündüz çalışıyoruz. Birlikte daha güzel bir Kırklareli inşa edeceğiz.',
};

export const mockRoutes: RouteSchedule[] = [
  {
    id: 'sehir',
    label: 'Şehir İçi',
    color: '#00668a',
    weekday: ['06:30', '07:15', '08:00', '09:00', '12:00', '17:30', '18:30', '20:00'],
    weekend: ['07:00', '09:00', '11:00', '14:00', '17:00', '19:00'],
    notes: { '08:00': 'Yoğun Saat', '17:30': 'Ekspres' },
  },
  {
    id: 'servis1',
    label: 'Servis 1',
    color: '#476272',
    weekday: ['06:45', '07:30', '08:15', '12:30', '17:00', '18:00'],
    weekend: ['08:00', '12:00', '16:00'],
  },
  {
    id: 'servis2',
    label: 'Servis 2',
    color: '#875205',
    weekday: ['07:00', '08:30', '13:00', '17:15', '19:30'],
    weekend: ['09:30', '14:30'],
    notes: { '13:00': 'Öğle' },
  },
];

export const mockDirectory = [
  { id: 1, name: 'Belediye Başkanlığı', phone: '444 01 39', address: 'Cumhuriyet Meydanı, Kırklareli' },
  { id: 2, name: 'Zabıta Müdürlüğü', phone: '0288 214 10 00', address: 'Merkez, Kırklareli' },
  { id: 3, name: 'Fen İşleri Müdürlüğü', phone: '0288 214 10 01', address: 'Merkez, Kırklareli' },
  { id: 4, name: 'İmar ve Şehircilik', phone: '0288 214 10 02', address: 'Merkez, Kırklareli' },
  { id: 5, name: 'Alo Belediye', phone: '153', address: '7/24 Hizmet Hattı' },
];

export const mockInfrastructureWorks: InfrastructureWork[] = [
  {
    id: 1,
    title: 'Merkez Mahallesi İçme Suyu Hat Yenileme',
    summary:
      'Eski asbest hatların PE boru ile değiştirilmesi ve vanaların yenilenmesi çalışması devam ediyor. Çalışma saatleri: 08:00–17:00.',
    location: 'Merkez Mahallesi',
    status: 'ongoing',
    statusLabel: 'Devam Ediyor',
    progress: 65,
    startedAt: '2026-05-12',
    estimatedEndAt: '2026-08-30',
    formattedStartDate: '12 Mayıs 2026',
    formattedEndDate: '30 Ağustos 2026',
  },
  {
    id: 2,
    title: 'Karacaibrahim Caddesi Asfalt Kaplama',
    summary:
      'Yol genişletme sonrası binder ve aşınma tabakası asfalt uygulaması planlandı. Trafik tek yönlü düzenlenecektir.',
    location: 'Karacaibrahim Cad.',
    status: 'planned',
    statusLabel: 'Planlandı',
    progress: 10,
    startedAt: '2026-07-01',
    estimatedEndAt: '2026-09-15',
    formattedStartDate: '1 Temmuz 2026',
    formattedEndDate: '15 Eylül 2026',
  },
  {
    id: 3,
    title: 'İstasyon Mahallesi Yağmur Suyu Kanalı',
    summary:
      'Sel riskini azaltmak için 420 metre yağmur suyu hattı döşendi. Kaldırım düzenlemesi tamamlandı.',
    location: 'İstasyon Mahallesi',
    status: 'completed',
    statusLabel: 'Tamamlandı',
    progress: 100,
    startedAt: '2026-03-01',
    estimatedEndAt: '2026-06-20',
    formattedStartDate: '1 Mart 2026',
    formattedEndDate: '20 Haziran 2026',
  },
  {
    id: 4,
    title: 'Pazaryeri Çevresi Kaldırım ve Aydınlatma',
    summary:
      'Pazar alanı çevresinde kaldırımların yenilenmesi ve LED sokak aydınlatması montajı sürüyor.',
    location: 'Pazaryeri / Cumhuriyet Cad.',
    status: 'ongoing',
    statusLabel: 'Devam Ediyor',
    progress: 40,
    startedAt: '2026-06-10',
    estimatedEndAt: '2026-10-01',
    formattedStartDate: '10 Haziran 2026',
    formattedEndDate: '1 Ekim 2026',
  },
];
