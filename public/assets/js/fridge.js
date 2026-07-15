

const CATEGORY_ICONS = {
  dairy: "🥛",
  meat: "🥩",
  chicken: "🍗",
  fish: "🐟",
  vegetable: "🥦",
  fruit: "🍎",
  drink: "🥤",
  frozen: "❄️",
  grain: "🌾",
  spice: "🧂",
  bakery: "🍞",
  sweet: "🍫",
  other: "🍽️"
};
function getCategoryIcon(category) {
  return CATEGORY_ICONS[category] || "🍽️";
}

const PRODUCT_EMOJI = {

  milk:"🥛", yogurt:"🥛", strained_yogurt:"🥛", ayran:"🥛", kefir:"🥛",
  kasar:"🧀", white_cheese:"🧀", lor:"🧀", mozzarella:"🧀", butter:"🧈",
  cream:"🥛", mascarpone_x:"🧀", parmesan_x:"🧀", gouda_x:"🧀", edam_x:"🧀",
  strained_cheese_x:"🧀", homemade_ayran_x:"🥛", egg:"🥚",

  beef:"🥩", minced_meat:"🥩", cubed_meat:"🥩", lamb_meat_x:"🥩",
  lamb_chops_x:"🥩", beef_liver_x:"🫀", lamb_liver_x:"🫀", tripe_x:"🥩",
  kavurma_x:"🥩", tenderloin:"🥩", ribeye:"🥩", meatball:"🍖",
  ready_kofte:"🍖", frozen_meatballs_x:"🍖",

  chicken_breast:"🍗", chicken_leg:"🍗", chicken_wings:"🍗",
  chicken_chop:"🍗", chicken_drumstick_x:"🍗", chicken_liver_x:"🍗",
  chicken_schnitzel_x:"🍗", chicken_doner_x:"🌯", chicken_meatball_x:"🍗",
  frozen_chicken_x:"🍗",

  fish:"🐟", salmon:"🐟", anchovy_x:"🐟", sea_bass_x:"🐟",
  gilthead_bream_x:"🐟", mackerel_x:"🐟", sardine_x:"🐟", hake_x:"🐟",
  trout_x:"🐟", shrimp:"🦐", squid:"🦑", frozen_fish_x:"🐟",

  tomato:"🍅", cucumber:"🥒", pepper:"🫑", carrot:"🥕", zucchini:"🥬",
  eggplant:"🍆", broccoli:"🥦", lettuce:"🥬", spinach:"🥬", onion:"🧅",
  garlic:"🧄", potato:"🥔", mushroom:"🍄", parsley:"🌿", dill:"🌿",
  spring_onion:"🌿", cauliflower:"🥦", leek:"🌿", celery_root:"🌿",
  cabbage:"🥬", red_cabbage:"🥬", arugula:"🥬", cress:"🌿",
  beetroot:"🫚", asparagus:"🌿", corn:"🌽", peas:"🫛",
  fresh_green_beans_x:"🫛", brussels_sprouts_x:"🥦",

  banana:"🍌", apple:"🍎", pear:"🍐", orange:"🍊", mandarin:"🍊",
  strawberry:"🍓", grape:"🍇", lemon:"🍋", pomegranate:"🍎", kiwi:"🥝",
  pineapple:"🍍", avocado:"🥑", peach:"🍑", apricot:"🍑", cherry:"🍒",
  sour_cherry:"🍒", watermelon:"🍉", melon:"🍈", fig:"🍑", dates:"🌴",
  blackberry_x:"🫐", raspberry_x:"🍓", blueberry_x:"🫐",
  grapefruit_x:"🍊", plum_x:"🍑", mango_x:"🥭", coconut_x:"🥥",

  ice_cream:"🍦", frozen_peas:"🫛", frozen_corn:"🌽", frozen_spinach:"🥬",
  frozen_broccoli:"🥦", frozen_fries:"🍟", frozen_veg_mix_x:"🥦",
  frozen_borek_x:"🫓",

  water:"💧", mineral_water:"💧", cola:"🥤", soda:"🥤", juice:"🧃",
  energy_drink:"⚡", iced_tea:"🍵", salgam_x:"🥤", kombucha_x:"🧃",
  iced_latte_x:"☕", filter_coffee_x:"☕", turkish_coffee_x:"☕",

  rice:"🍚", bulgur:"🌾", pasta:"🍝", eriste:"🍜", orzo_x:"🍚",
  vermicelli_x:"🍜", couscous_x:"🌾",
  red_lentil:"🫘", green_lentil:"🫘", chickpea:"🫘", dry_bean:"🫘",
  borlotti_bean:"🫘", cowpea:"🫘", black_bean:"🫘", soybean:"🫘",
  fava_bean:"🫘", oat:"🌾", oatmeal:"🌾", rye:"🌾", quinoa:"🌾",
  barley:"🌾", wheat:"🌾", cornmeal:"🌽", whole_flour:"🌾", flour:"🌾",

  chili_flakes:"🌶️", black_pepper:"🧂", cumin:"🌿", turmeric:"🟡",
  cinnamon:"🍂", thyme:"🌿", mint:"🌿", ginger:"🫚", basil:"🌿",
  curry:"🟡", sumac:"🌶️", paprika:"🌶️", saffron:"🌼",

  bread:"🍞", whole_bread:"🍞", pide:"🫓", lavash:"🫓", simit:"🥯",
  borek:"🥐", yufka:"🫓",

  walnut:"🫘", almond:"🫘", hazelnut:"🌰", peanut:"🥜",
  pistachio:"🫘", cashew:"🫘", raisin:"🍇", dried_apricot:"🍑",
  dried_fig:"🍑",

  salt:"🧂", sugar:"🍬", honey:"🍯", vinegar:"🫙", ketchup:"🍅",
  mayonnaise:"🫙", tomato_paste:"🍅", jam:"🍯",
  olive_oil:"🫒", sunflower_oil:"🫙", soy_sauce:"🫙", mustard:"🟡",
  olive:"🫒", pickle:"🥒"
};

function getProductEmoji(product) {
  if (!product) return "🍽️";

  if (product.key && PRODUCT_EMOJI[product.key]) return PRODUCT_EMOJI[product.key];

  return CATEGORY_ICONS[product.category] || "🍽️";
}

const productDB = [

    { name: "Süt", key: "milk", category: "dairy" },
  { name: "Yoğurt", key: "yogurt", category: "dairy" },
  { name: "Süzme Yoğurt", key: "strained_yogurt", category: "dairy" },
  { name: "Ayran", key: "ayran", category: "dairy" },
  { name: "Kefir", key: "kefir", category: "dairy" },
  { name: "Kaşar Peyniri", key: "kasar", category: "dairy" },
  { name: "Beyaz Peynir", key: "white_cheese", category: "dairy" },
  { name: "Lor Peyniri", key: "lor", category: "dairy" },
  { name: "Mozzarella", key: "mozzarella", category: "dairy" },
  { name: "Tereyağı", key: "butter", category: "dairy" },
  { name: "Krema", key: "cream", category: "dairy" },
  { name: "Mascarpone", key: "mascarpone_x", category: "dairy" },
  { name: "Parmesan", key: "parmesan_x", category: "dairy" },
  { name: "Gouda", key: "gouda_x", category: "dairy" },
  { name: "Edam", key: "edam_x", category: "dairy" },
  { name: "Süzme Peynir", key: "strained_cheese_x", category: "dairy" },
  { name: "Ayran (Ev Yapımı)", key: "homemade_ayran_x", category: "dairy" },

    { name: "Yumurta", key: "egg", category: "dairy" },

    { name: "Dana Eti", key: "beef", category: "meat" },
  { name: "Kıyma", key: "minced_meat", category: "meat" },
  { name: "Kuşbaşı", key: "cubed_meat", category: "meat" },
  { name: "Kuzu Eti", key: "lamb_meat_x", category: "meat" },
  { name: "Kuzu Pirzola", key: "lamb_chops_x", category: "meat" },
  { name: "Dana Ciğer", key: "beef_liver_x", category: "meat" },
  { name: "Kuzu Ciğer", key: "lamb_liver_x", category: "meat" },
  { name: "İşkembe", key: "tripe_x", category: "meat" },
  { name: "Kavurma", key: "kavurma_x", category: "meat" },
  { name: "Bonfile", key: "tenderloin", category: "meat" },
  { name: "Antrikot", key: "ribeye", category: "meat" },
  { name: "Köfte", key: "meatball", category: "meat" },
  { name: "Hazır Köfte", key: "ready_kofte", category: "meat" },
  { name: "Donuk Köfte", key: "frozen_meatballs_x", category: "frozen" },

    { name: "Tavuk Göğüs", key: "chicken_breast", category: "chicken" },
  { name: "Tavuk But", key: "chicken_leg", category: "chicken" },
  { name: "Tavuk Kanat", key: "chicken_wings", category: "chicken" },
  { name: "Tavuk Pirzola", key: "chicken_chop", category: "chicken" },
  { name: "Tavuk Baget", key: "chicken_drumstick_x", category: "chicken" },
  { name: "Tavuk Ciğer", key: "chicken_liver_x", category: "chicken" },
  { name: "Tavuk Şinitzel", key: "chicken_schnitzel_x", category: "chicken" },
  { name: "Tavuk Döner", key: "chicken_doner_x", category: "chicken" },
  { name: "Tavuk Köfte", key: "chicken_meatball_x", category: "chicken" },
  { name: "Donuk Tavuk", key: "frozen_chicken_x", category: "frozen" },

    { name: "Balık", key: "fish", category: "fish" },
  { name: "Somon", key: "salmon", category: "fish" },
  { name: "Hamsi", key: "anchovy_x", category: "fish" },
  { name: "Levrek", key: "sea_bass_x", category: "fish" },
  { name: "Çupra", key: "gilthead_bream_x", category: "fish" },
  { name: "Uskumru", key: "mackerel_x", category: "fish" },
  { name: "Sardalya", key: "sardine_x", category: "fish" },
  { name: "Mezgit", key: "hake_x", category: "fish" },
  { name: "Alabalık", key: "trout_x", category: "fish" },
  { name: "Karides", key: "shrimp", category: "fish" },
  { name: "Kalamar", key: "squid", category: "fish" },
  { name: "Donuk Balık", key: "frozen_fish_x", category: "frozen" },

    { name: "Domates", key: "tomato", category: "vegetable" },
  { name: "Salatalık", key: "cucumber", category: "vegetable" },
  { name: "Biber", key: "pepper", category: "vegetable" },
  { name: "Havuç", key: "carrot", category: "vegetable" },
  { name: "Kabak", key: "zucchini", category: "vegetable" },
  { name: "Patlıcan", key: "eggplant", category: "vegetable" },
  { name: "Brokoli", key: "broccoli", category: "vegetable" },
  { name: "Marul", key: "lettuce", category: "vegetable" },
  { name: "Ispanak", key: "spinach", category: "vegetable" },
  { name: "Soğan", key: "onion", category: "vegetable" },
  { name: "Sarımsak", key: "garlic", category: "vegetable" },
  { name: "Patates", key: "potato", category: "vegetable" },
  { name: "Mantar", key: "mushroom", category: "vegetable" },
  { name: "Maydanoz", key: "parsley", category: "vegetable" },
  { name: "Dereotu", key: "dill", category: "vegetable" },
  { name: "Taze Soğan", key: "spring_onion", category: "vegetable" },
  { name: "Karnabahar", key: "cauliflower", category: "vegetable" },
  { name: "Pırasa", key: "leek", category: "vegetable" },
  { name: "Kereviz", key: "celery_root", category: "vegetable" },
  { name: "Lahana", key: "cabbage", category: "vegetable" },
  { name: "Kırmızı Lahana", key: "red_cabbage", category: "vegetable" },
  { name: "Roka", key: "arugula", category: "vegetable" },
  { name: "Tere", key: "cress", category: "vegetable" },
  { name: "Pancar", key: "beetroot", category: "vegetable" },
  { name: "Kuşkonmaz", key: "asparagus", category: "vegetable" },
  { name: "Mısır", key: "corn", category: "vegetable" },
  { name: "Bezelye", key: "peas", category: "vegetable" },
  { name: "Taze Fasulye", key: "fresh_green_beans_x", category: "vegetable" },
  { name: "Brüksel Lahanası", key: "brussels_sprouts_x", category: "vegetable" },

    { name: "Muz", key: "banana", category: "fruit" },
  { name: "Elma", key: "apple", category: "fruit" },
  { name: "Armut", key: "pear", category: "fruit" },
  { name: "Portakal", key: "orange", category: "fruit" },
  { name: "Mandalina", key: "mandarin", category: "fruit" },
  { name: "Çilek", key: "strawberry", category: "fruit" },
  { name: "Üzüm", key: "grape", category: "fruit" },
  { name: "Limon", key: "lemon", category: "fruit" },
  { name: "Nar", key: "pomegranate", category: "fruit" },
  { name: "Kivi", key: "kiwi", category: "fruit" },
  { name: "Ananas", key: "pineapple", category: "fruit" },
  { name: "Avokado", key: "avocado", category: "fruit" },
  { name: "Şeftali", key: "peach", category: "fruit" },
  { name: "Kayısı", key: "apricot", category: "fruit" },
  { name: "Kiraz", key: "cherry", category: "fruit" },
  { name: "Vişne", key: "sour_cherry", category: "fruit" },
  { name: "Karpuz", key: "watermelon", category: "fruit" },
  { name: "Kavun", key: "melon", category: "fruit" },
  { name: "İncir", key: "fig", category: "fruit" },
  { name: "Hurma", key: "dates", category: "fruit" },
  { name: "Böğürtlen", key: "blackberry_x", category: "fruit" },
  { name: "Ahududu", key: "raspberry_x", category: "fruit" },
  { name: "Yaban Mersini", key: "blueberry_x", category: "fruit" },
  { name: "Greyfurt", key: "grapefruit_x", category: "fruit" },
  { name: "Erik", key: "plum_x", category: "fruit" },
  { name: "Mango", key: "mango_x", category: "fruit" },
  { name: "Hindistan Cevizi", key: "coconut_x", category: "fruit" },

    { name: "Dondurma", key: "ice_cream", category: "frozen" },
  { name: "Dondurulmuş Bezelye", key: "frozen_peas", category: "frozen" },
  { name: "Dondurulmuş Mısır", key: "frozen_corn", category: "frozen" },
  { name: "Dondurulmuş Ispanak", key: "frozen_spinach", category: "frozen" },
  { name: "Dondurulmuş Brokoli", key: "frozen_broccoli", category: "frozen" },
  { name: "Dondurulmuş Patates", key: "frozen_fries", category: "frozen" },
  { name: "Donuk Sebze Karışımı", key: "frozen_veg_mix_x", category: "frozen" },
  { name: "Donuk Börek", key: "frozen_borek_x", category: "frozen" },

    { name: "Su", key: "water", category: "drink" },
  { name: "Maden Suyu", key: "mineral_water", category: "drink" },
  { name: "Kola", key: "cola", category: "drink" },
  { name: "Gazoz", key: "soda", category: "drink" },
  { name: "Meyve Suyu", key: "juice", category: "drink" },
  { name: "Enerji İçeceği", key: "energy_drink", category: "drink" },
  { name: "Soğuk Çay", key: "iced_tea", category: "drink" },
  { name: "Şalgam", key: "salgam_x", category: "drink" },
  { name: "Kombucha", key: "kombucha_x", category: "drink" },
  { name: "Sütlü Kahve", key: "iced_latte_x", category: "drink" },
  { name: "Filtre Kahve", key: "filter_coffee_x", category: "drink" },
  { name: "Türk Kahvesi", key: "turkish_coffee_x", category: "drink" },

    { name: "Pirinç", key: "rice", category: "grain" },
  { name: "Bulgur", key: "bulgur", category: "grain" },
  { name: "Makarna", key: "pasta", category: "grain" },
  { name: "Erişte", key: "eriste", category: "grain" },
  { name: "Arpa Şehriye", key: "orzo_x", category: "grain" },
  { name: "Tel Şehriye", key: "vermicelli_x", category: "grain" },
  { name: "Kuskus", key: "couscous_x", category: "grain" },

    { name: "Kırmızı Mercimek", key: "red_lentil", category: "grain" },
  { name: "Yeşil Mercimek", key: "green_lentil", category: "grain" },
  { name: "Nohut", key: "chickpea", category: "grain" },
  { name: "Kuru Fasulye", key: "dry_bean", category: "grain" },
  { name: "Barbunya", key: "borlotti_bean", category: "grain" },
  { name: "Börülce", key: "cowpea", category: "grain" },
  { name: "Siyah Fasulye", key: "black_bean", category: "grain" },
  { name: "Soya Fasulyesi", key: "soybean", category: "grain" },
  { name: "Bakla", key: "fava_bean", category: "grain" },
  { name: "Yulaf", key: "oat", category: "grain" },
  { name: "Yulaf Ezmesi", key: "oatmeal", category: "grain" },
  { name: "Çavdar", key: "rye", category: "grain" },
  { name: "Kinoa", key: "quinoa", category: "grain" },
  { name: "Arpa", key: "barley", category: "grain" },
  { name: "Buğday", key: "wheat", category: "grain" },
  { name: "Mısır Unu", key: "cornmeal", category: "grain" },
  { name: "Tam Buğday Unu", key: "whole_flour", category: "grain" },
  { name: "Un", key: "flour", category: "grain" },

    { name: "Pul Biber", key: "chili_flakes", category: "spice" },
  { name: "Karabiber", key: "black_pepper", category: "spice" },
  { name: "Kimyon", key: "cumin", category: "spice" },
  { name: "Zerdeçal", key: "turmeric", category: "spice" },
  { name: "Tarçın", key: "cinnamon", category: "spice" },
  { name: "Kekik", key: "thyme", category: "spice" },
  { name: "Nane", key: "mint", category: "spice" },
  { name: "Zencefil", key: "ginger", category: "spice" },
  { name: "Fesleğen", key: "basil", category: "spice" },
  { name: "Köri", key: "curry", category: "spice" },
  { name: "Sumak", key: "sumac", category: "spice" },
  { name: "Paprika", key: "paprika", category: "spice" },
  { name: "Safran", key: "saffron", category: "spice" },

    { name: "Ekmek", key: "bread", category: "bakery" },
  { name: "Tam Buğday Ekmeği", key: "whole_bread", category: "bakery" },
  { name: "Pide", key: "pide", category: "bakery" },
  { name: "Lavaş", key: "lavash", category: "bakery" },
  { name: "Simit", key: "simit", category: "bakery" },
  { name: "Börek", key: "borek", category: "bakery" },
  { name: "Yufka", key: "yufka", category: "bakery" },

    { name: "Ceviz", key: "walnut", category: "other" },
  { name: "Badem", key: "almond", category: "other" },
  { name: "Fındık", key: "hazelnut", category: "other" },
  { name: "Fıstık", key: "peanut", category: "other" },
  { name: "Antep Fıstığı", key: "pistachio", category: "other" },
  { name: "Kaju", key: "cashew", category: "other" },
  { name: "Kuru Üzüm", key: "raisin", category: "other" },
  { name: "Kuru Kayısı", key: "dried_apricot", category: "other" },
  { name: "Kuru İncir", key: "dried_fig", category: "other" },

    { name: "Tuz", key: "salt", category: "other" },
  { name: "Şeker", key: "sugar", category: "other" },
  { name: "Bal", key: "honey", category: "other" },
  { name: "Sirke", key: "vinegar", category: "other" },
  { name: "Ketçap", key: "ketchup", category: "other" },
  { name: "Mayonez", key: "mayonnaise", category: "other" },
  { name: "Salça", key: "tomato_paste", category: "other" },
  { name: "Reçel", key: "jam", category: "other" },
  { name: "Zeytinyağı", key: "olive_oil", category: "other" },
  { name: "Ayçiçek Yağı", key: "sunflower_oil", category: "other" },
  { name: "Soya Sosu", key: "soy_sauce", category: "other" },
  { name: "Hardal", key: "mustard", category: "other" },
  { name: "Zeytin", key: "olive", category: "other" },
  { name: "Turşu", key: "pickle", category: "other" }
];

let lastSelectedProduct = null;

const FALLBACK_ICON = "https://cdn-icons-png.flaticon.com/512/1828/1828884.png";

async function fridgeApi(action, body = null) {
  const map = {
    'fridge_list':   { path: 'api/fridge', method: 'GET' },
    'fridge_add':    { path: 'api/fridge', method: 'POST' },
    'fridge_delete': { path: 'api/fridge/' + (body ? body.id : ''), method: 'DELETE' },
  };
  const entry = map[action] || { path: 'api/' + action, method: body ? 'POST' : 'GET' };
  try {
    if (entry.method === 'GET') {
      const res = await fetch(entry.path, { method: 'GET' });
      return await res.json();
    }
    const res = await fetch(entry.path, {
      method: entry.method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body || {})
    });
    return await res.json();
  } catch (e) {
    return { success: false, message: "Bağlantı hatası" };
  }
}

const CUSTOM_KEY = "customProducts";

function loadCustomProducts() {
  try {
    return JSON.parse(localStorage.getItem(CUSTOM_KEY) || "[]");
  } catch {
    return [];
  }
}

function saveCustomProducts(arr) {
  localStorage.setItem(CUSTOM_KEY, JSON.stringify(arr));
}

function getAllProducts() {
  return [...productDB, ...loadCustomProducts()];
}

function slugifyTR(str) {
  return str
    .toLowerCase()
    .trim()
    .replace(/ğ/g, "g").replace(/ü/g, "u").replace(/ş/g, "s").replace(/ı/g, "i").replace(/ö/g, "o").replace(/ç/g, "c")
    .replace(/[^a-z0-9]+/g, "_")
    .replace(/^_+|_+$/g, "");
}

// makeKey: Özel ürün için benzersiz anahtar üretir.
//   slugifyTR(ad) + "_" + timestamp → çakışma riski yoktur.
function makeKey(name) {
  return slugifyTR(name) + "_" + Date.now();
}

function fileToDataURL(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => resolve(reader.result);
    reader.onerror = reject;
    reader.readAsDataURL(file);
  });
}

function shelfLabel(shelf) {
  return shelf === "shelf-1" ? "Üst Raf" : shelf === "shelf-2" ? "Alt Raf" : shelf === "freezer-shelf" ? "Buzluk" : "Raf";
}

function daysLeft(expiryDateStr) {
  if (!expiryDateStr) return null;
  const exp = new Date(expiryDateStr);
  if (Number.isNaN(exp.getTime())) return null;
  const now = new Date();
  return Math.ceil((exp - now) / 86400000);
}

async function loadFridgeFromDB() {
  const r = await fridgeApi("fridge_list");
  if (!r || !r.success) {
    console.warn("fridge_list hata:", r?.message);
    return;
  }

  const items = r.data || [];

  ["shelf-1", "shelf-2", "freezer-shelf"].forEach((id) => {
    const el = document.getElementById(id);
    if (el) el.innerHTML = "";
  });

  // Panel listeyi temizle
  const inv = document.getElementById("inventoryList");
  if (inv) inv.innerHTML = "";

  items.forEach((item) => {
    // Alan adı uyumluluğu: DB'den "icon" gelir
    const icon   = item.icon || item.icon_url || FALLBACK_ICON;
    const shelf  = item.shelf || "shelf-1";

    const expiry = item.expiry_date || item.expiry || null;

    const diff       = daysLeft(expiry);
    const isCritical = diff !== null && diff <= 3;

    let expiryLabel = "";
    if (diff !== null) {
      if (diff < 0)       expiryLabel = `<span style="color:#ff4d4d">Süresi Dolmuş</span>`;
      else if (diff === 0) expiryLabel = `<span style="color:#ff4d4d">⛔ Bugün bitiyor</span>`;
      else if (diff === 1) expiryLabel = `<span style="color:#ff8800">🚨 1 Gün kaldı</span>`;
      else if (diff <= 3)  expiryLabel = `<span style="color:#ffd700">⚠️ ${diff} Gün kaldı</span>`;
      else                 expiryLabel = `<span>${diff} Gün kaldı</span>`;
    }

    // Panel kartı
    const card = document.createElement("div");
    card.className = "inv-card";
    card.dataset.dbId = item.id;

    // Ürün adına göre emoji bul; yoksa kategori ikonuna
    const _pmLoad = getAllProducts().find(p => p.name === item.name);
    const _emojiLoad = _pmLoad ? getProductEmoji(_pmLoad) : null;
    const _iconHtmlLoad = _emojiLoad
      ? `<span class="inv-emoji">${_emojiLoad}</span>`
      : `<img src="${icon}" onerror="this.src='${FALLBACK_ICON}'">`;

    card.innerHTML = `
      ${_iconHtmlLoad}
      <div class="inv-info">
        <span class="inv-name">${item.name}</span>
        <div class="inv-meta">
          <span><i class="fas fa-map-marker-alt"></i> ${shelfLabel(shelf)}</span>
          ${expiryLabel ? ` • ${expiryLabel}` : ""}
        </div>
      </div>
      <button class="btn-consume" onclick="consumeDBItem(this, ${item.id})">TÜKET</button>
      <div class="status-dot ${isCritical ? "status-warning" : ""}"></div>
    `;

    inv?.prepend(card);

    // Buzdolabı rafı — emoji göster
    const shelfEl = document.getElementById(shelf);
    if (shelfEl) {
      const productMatch = getAllProducts().find(p => p.name === item.name);
      const emoji = productMatch ? getProductEmoji(productMatch) : "🍽️";
      const span = document.createElement("div");
      span.dataset.dbId = item.id;
      span.title = item.name + (diff !== null ? ` (${diff >= 0 ? diff + " gün" : "süresi dolmuş"})` : "");
      span.style.cssText = "display:flex;flex-direction:column;align-items:center;cursor:pointer;margin:2px;";
      span.innerHTML = `<span style="font-size:2rem;filter:drop-shadow(0 2px 4px rgba(0,0,0,.3));">${emoji}</span><span style="font-size:9px;color:#111;text-align:center;font-weight:600;max-width:44px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">${item.name}</span>`;
      shelfEl.appendChild(span);
    }
  });
}

// ==========================
// DB ITEM TÜKET (sil + UI — hem liste hem raf)
// ==========================
// "TÜKET" butonuna tıklanınca çağrılır.
// İşleyiş:
//   1) Kart CSS ile sağa kaydırılıp şeffaflaştırılır (0.3s geçiş animasyonu)
//   2) fridgeApi("fridge_delete", { id: dbId }) → api.php → veritabanından silinir
//   3) Başarılıysa: 300ms sonra hem envanter kartı hem raftan ürün kaldırılır
//      (querySelectorAll(`[data-db-id="${dbId}"]`) ile her iki element de bulunur)
//   4) Başarısızsa: animasyon geri alınır, hata mesajı gösterilir
async function consumeDBItem(btn, dbId) {
  const card = btn.closest(".inv-card");
  if (card) {
    card.style.transition = "all 0.3s";
    card.style.transform = "translateX(50px)";
    card.style.opacity = "0";
  }

  const r = await fridgeApi("fridge_delete", { id: dbId });
  if (!r || !r.success) {
    if (card) { card.style.transform = ""; card.style.opacity = ""; }
    alert("Silme başarısız: " + (r?.message || "Bilinmeyen hata"));
    return;
  }

  setTimeout(() => {
    // Panel kartını sil
    card?.remove();
    // Raftaki emoji/img elementlerini de sil (data-db-id eşleşmesi)
    document.querySelectorAll(`[data-db-id="${dbId}"]`).forEach((el) => el.remove());
  }, 300);
}

// ==========================
// DOOR / PANEL
// ==========================
// toggleDoor(side): Belirtilen kapıya (.door-{side}) .open class'ını toggle eder.
//   CSS'te .open → rotateY(±115deg) ile 3D kapı açılma efekti oluşur.
//   Herhangi bir kapı açıksa #mainWrapper'a .active-panel eklenir:
//     → .fridge-main-wrapper padding-left azalır (buzdolabı sola kayar)
//     → .side-ai-panel right: -420px → right: 40px kayar (envanter paneli görünür)
//   Tüm kapılar kapandığında .active-panel kaldırılır; panel tekrar gizlenir.
function toggleDoor(side) {
  const door = document.querySelector(`.door-${side}`);
  if (!door) return;
  door.classList.toggle("open");

  const anyOpen = document.querySelectorAll(".door.open").length > 0;
  document.getElementById("mainWrapper")?.classList.toggle("active-panel", anyOpen);

  // Mobilde panel görünür olunca smooth scroll ile aşağı git
  if (anyOpen && window.innerWidth <= 900) {
    setTimeout(() => {
      const panel = document.getElementById("sidePanel");
      if (panel) panel.scrollIntoView({ behavior: "smooth", block: "nearest" });
    }, 400);
  }
}

// ==========================
// MODAL OPEN/CLOSE
// ==========================
// openAddModal():
//   - Modalı display:flex ile gösterir
//   - Arama kutusunu ve seçili ürünü sıfırlar (lastSelectedProduct = null)
//   - "Seçildi" rozetini ve özel ürün kutusunu gizler
//   - Tüm ürünleri listeleyerek başlangıç aramayı doldurur (renderSearchResults)
// closeAddModal(): Modalı display:none ile gizler.
function openAddModal() {
  document.getElementById("addModal").style.display = "flex";

  document.getElementById("productSearch").value = "";
  lastSelectedProduct = null;

  document.getElementById("selectedDisplay").classList.add("d-none");
  document.getElementById("searchResults").classList.remove("d-none");
  document.getElementById("createNewBox").classList.add("d-none");

  const npn = document.getElementById("newProductName");
  if (npn) npn.value = "";

  renderSearchResults(getAllProducts());
}

function closeAddModal() {
  document.getElementById("addModal").style.display = "none";
}

// ==========================
// SEARCH + RENDER
// ==========================
// renderSearchResults(list): Ürün listesini arama sonuç kutusuna çizer.
//   Her satır: emoji ikonu + ürün adı.
//   Satıra tıklanınca lastSelectedProduct ayarlanır ve "Seçildi" rozeti gösterilir.
//   Performans için max 120 ürün listelenir (slice(0, 120)).
//
// searchInDatabase(): #productSearch input'undan sorgu alır, getAllProducts()
//   üzerinde toLowerCase().includes() ile filtreler, renderSearchResults() çağırır.
//   Sorgu ≥ 2 karakter ve sonuç boşsa #createNewBox (özel ürün kutusu) gösterilir.
function renderSearchResults(list) {
  const resultsDiv = document.getElementById("searchResults");
  if (!resultsDiv) return;

  resultsDiv.innerHTML = "";
  const limited = list.slice(0, 120);

  limited.forEach((p) => {
    const row = document.createElement("div");
    row.className = "search-row";

    const emoji = getProductEmoji(p);

    row.innerHTML = `<span style="font-size:1.3rem;min-width:26px;">${emoji}</span> <span>${p.name}</span>`;
    row.onclick = () => {
      lastSelectedProduct = p;

      const badge = document.getElementById("selectedDisplay");
      badge.innerHTML = `<span style="font-size:1.1rem;">${emoji}</span> <b>${p.name} Seçildi</b>`;
      badge.classList.remove("d-none");

      resultsDiv.classList.add("d-none");
      document.getElementById("createNewBox").classList.add("d-none");
    };

    resultsDiv.appendChild(row);
  });
}

function searchInDatabase() {
  const q = document.getElementById("productSearch").value.toLowerCase().trim();
  const resultsDiv = document.getElementById("searchResults");
  const createBox = document.getElementById("createNewBox");

  resultsDiv.classList.remove("d-none");

  const all = getAllProducts();
  const filtered = all.filter((p) => p.name.toLowerCase().includes(q));
  renderSearchResults(filtered);

  if (q.length >= 2 && filtered.length === 0) {
    createBox.classList.remove("d-none");
    document.getElementById("newProductName").value = document.getElementById("productSearch").value;
  } else {
    createBox.classList.add("d-none");
  }
}

// ==========================
// CREATE CUSTOM PRODUCT
// ==========================
// Veritabanında olmayan ürünler için özel kayıt oluşturur.
// İşleyiş:
//   1) Ürün adı zorunludur
//   2) { name, key: makeKey(ad), icon: FALLBACK_ICON, custom: true } objesi oluşturulur
//   3) Aynı isimde ürün varsa tekrar eklenmez, mevcut olan seçilir
//   4) saveCustomProducts() ile localStorage'a yazılır
//   5) lastSelectedProduct ayarlanır → kullanıcı raf/SKT seçip "İŞLE" diyebilir
async function createCustomProductFromModal() {
  const nameInput = document.getElementById("newProductName");

  const name = (nameInput.value || "").trim();

  if (!name) return alert("Ürün adı zorunlu!");

  const newItem = { name, key: makeKey(name), icon: FALLBACK_ICON, custom: true };

  const customs = loadCustomProducts();
  const already = customs.some((x) => x.name.toLowerCase() === name.toLowerCase());
  if (!already) {
    customs.unshift(newItem);
    saveCustomProducts(customs);
    lastSelectedProduct = newItem;
  } else {
    lastSelectedProduct = customs.find((x) => x.name.toLowerCase() === name.toLowerCase());
  }

  const badge = document.getElementById("selectedDisplay");
  badge.innerHTML = `<img src="${lastSelectedProduct.icon}" width="18" height="18" style="object-fit:contain;"> <b>${lastSelectedProduct.name} Seçildi</b>`;
  badge.classList.remove("d-none");

  document.getElementById("searchResults").classList.add("d-none");
  document.getElementById("createNewBox").classList.add("d-none");
}

// ==========================
// ADD ITEM → DB'ye kaydet + UI
// ==========================
// Modal'daki "SİSTEME İŞLE" butonuna tıklanınca çağrılır.
// İşleyiş:
//   1) lastSelectedProduct ve SKT kontrolü yapılır
//   2) fridgeApi("fridge_add", { name, icon_url, shelf, expiry_date }) → veritabanına kaydedilir
//      (api.php hem icon_url hem icon, hem expiry_date hem expiry alanlarını kabul eder)
//   3) daysLeft() ile SKT hesaplanır; 3 gün altı → isCritical = true
//   4) expiryLabel → renkli HTML SKT metni (kırmızı/turuncu/sarı/beyaz)
//   5) #inventoryList'e yeni inv-card eklenir:
//      - Emoji varsa: <span class="inv-emoji">
//      - Yoksa: <img src="..."> (FALLBACK_ICON ile yedeklenir)
//      - "TÜKET" butonu: consumeDBItem(this, dbId) çağırır
//      - .status-dot: isCritical ise .status-warning (kırmızı LED)
//   6) İlgili raf div'ine (shelf-1, shelf-2 veya freezer-shelf) emoji + ad div'i eklenir
//      (data-db-id ile consumeDBItem silerken bu element de kaldırılır)
//   7) Modal kapatılır, lastSelectedProduct = null sıfırlanır
async function addNewItem() {
  if (!lastSelectedProduct) return alert("Lütfen bir ürün seçin!");

  const shelfSelect = document.getElementById("shelfSelect");
  const shelfId = shelfSelect.value;
  const shelfName = shelfSelect.options[shelfSelect.selectedIndex].text;

  const expiry = document.getElementById("expiryDateInput").value;
  if (!expiry) return alert("Lütfen S.K.T girin!");

  const icon = lastSelectedProduct.icon || FALLBACK_ICON;

  // Backend ile UYUMLU alan adları (api.php: icon_url ?? icon, expiry_date ?? expiry destekliyor)
  const r = await fridgeApi("fridge_add", {
    name:        lastSelectedProduct.name,
    icon_url:    icon,          // api.php: $_POST['icon_url'] ?? $_POST['icon']
    shelf:       shelfId,
    expiry_date: expiry,        // api.php: $_POST['expiry_date'] ?? $_POST['expiry']
  });

  if (!r || !r.success) {
    alert("Kayıt hatası: " + (r?.message || "Bilinmeyen hata"));
    return;
  }

  const dbId = r.data?.id;
  const diff = daysLeft(expiry);
  const isCritical = diff !== null && diff <= 3;

  // Expiry gösterim metni (addNewItem için de tutarlı)
  let expiryLabel = "";
  if (diff !== null) {
    if (diff < 0)        expiryLabel = `<span style="color:#ff4d4d">Süresi Dolmuş</span>`;
    else if (diff === 0) expiryLabel = `<span style="color:#ff4d4d">⛔ Bugün bitiyor</span>`;
    else if (diff === 1) expiryLabel = `<span style="color:#ff8800">🚨 1 Gün kaldı</span>`;
    else if (diff <= 3)  expiryLabel = `<span style="color:#ffd700">⚠️ ${diff} Gün kaldı</span>`;
    else                 expiryLabel = `<span>${diff} Gün kaldı</span>`;
  }

  // Panel kartı
  const card = document.createElement("div");
  card.className = "inv-card";
  card.dataset.dbId = dbId;

  const _emojiAdd = getProductEmoji(lastSelectedProduct);
  const _iconHtmlAdd = _emojiAdd && _emojiAdd !== "🍽️"
    ? `<span class="inv-emoji">${_emojiAdd}</span>`
    : (icon !== FALLBACK_ICON
        ? `<img src="${icon}" onerror="this.src='${FALLBACK_ICON}'">`
        : `<span class="inv-emoji">🍽️</span>`);

  card.innerHTML = `
    ${_iconHtmlAdd}
    <div class="inv-info">
      <span class="inv-name">${lastSelectedProduct.name}</span>
      <div class="inv-meta">
        <span><i class="fas fa-map-marker-alt"></i> ${shelfName}</span>
        ${expiryLabel ? ` • ${expiryLabel}` : ""}
      </div>
    </div>
    <button class="btn-consume" onclick="consumeDBItem(this, ${dbId})">TÜKET</button>
    <div class="status-dot ${isCritical ? "status-warning" : ""}"></div>
  `;

  document.getElementById("inventoryList")?.prepend(card);

  // Buzdolabı rafı — emoji
  const shelfEl = document.getElementById(shelfId);
  if (shelfEl) {
    const emoji = getProductEmoji(lastSelectedProduct);
    const span = document.createElement("div");
    span.dataset.dbId = dbId;
    span.title = lastSelectedProduct.name + (diff !== null ? ` (${diff} gün)` : "");
    span.style.cssText = "display:flex;flex-direction:column;align-items:center;cursor:pointer;margin:2px;";
    span.innerHTML = `<span style="font-size:2rem;filter:drop-shadow(0 2px 4px rgba(0,0,0,.3));">${emoji}</span><span style="font-size:9px;color:#111;text-align:center;font-weight:600;max-width:44px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">${lastSelectedProduct.name}</span>`;
    shelfEl.appendChild(span);
  }

  closeAddModal();
  lastSelectedProduct = null;
  document.getElementById("selectedDisplay")?.classList.add("d-none");

  try { window.parent.postMessage({ type: 'notify_refresh' }, '*'); } catch(e) {}
}

let currentTemp = 3;

function openTempControl(event) {
  event.stopPropagation();
  const panel = document.getElementById("tempControlPanel");
  if (!panel) return;

  panel.style.display = "block";
  panel.style.top = event.pageY + 20 + "px";
  panel.style.left = event.pageX - 70 + "px";
}

function changeTemp(amount) {
  currentTemp += amount;
  if (currentTemp < -20) currentTemp = -20;
  if (currentTemp > 10) currentTemp = 10;

  const t1 = document.getElementById("tempDisplay");
  const t2 = document.getElementById("targetTemp");
  if (t1) t1.innerText = currentTemp + "°C";
  if (t2) t2.innerText = currentTemp + "°C";
}

window.addEventListener("click", (event) => {
  if (!event.target.matches(".smart-tag") && !event.target.closest("#tempControlPanel")) {
    const panel = document.getElementById("tempControlPanel");
    if (panel) panel.style.display = "none";
  }
});

document.addEventListener("DOMContentLoaded", () => {
  loadFridgeFromDB();
});