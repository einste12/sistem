const mysql = require("mysql");
const axios = require("axios");
const cron = require("node-cron");

const pool = mysql.createPool({
  connectionLimit: 10,
  host: "104.247.162.226", // Replace with your MySQL server host
  user: "gezenato_okadmin", // Replace with your MySQL user
  password: "E8F3SjCMWG8AP2CpmwuN", // Replace with your MySQL password
  database: "gezenato_oyunkampta", // Replace with your MySQL database name
  port: 3306,
});

async function getSalesData() {
  const url = "https://oyunkampta.com/wp-json/wc/v3/orders";
  const per_page = 100;
  const status = "completed,processing,on-hold,partially-paid";
  const username = 'ck_33fd4920c976eafdc9ba959dae3e869e922f7c27';
  const password = 'cs_1045de4488733b788b177ac9c69c4f83373301a1';

  let page = 1;
  let data = [];

  let response;
  do {
    response = await axios.get(`${url}?status=${status}&per_page=${per_page}&page=${page}`, {
      auth: {
        username: username,
        password: password,
      },
    });
    data = data.concat(response.data);
    page++;
  } while (response.data.length > 0);

  return data;
}

async function insertSalesData(salesData) {
  const currentTime = new Date();
  const threeHoursLater = new Date(currentTime.getTime() + 3 * 60 * 60 * 1000);
  const formattedTime = threeHoursLater.toISOString().slice(0, 19).replace("T", " ");
  const timeInsertSQL = `
    INSERT INTO time (run_time) VALUES (?)
  `;
  const timeInsertValues = [[formattedTime]];

  try {
    await pool.query(timeInsertSQL, [timeInsertValues]);
  } catch (error) {
    console.error(error);
    return;
  }

  const sql = `
  INSERT INTO sales (
    id,
    siparis_id,
    status,
    musteri_id,
    musteri_adi,
    vergi_no,
    telefon,
    kamp_adi,
    tarih,
    konaklama,
    paket,
    katilimci1_ad_soyad,
    katilimci1_tc,
    katilimci1_dt,
    katilimci2_ad_soyad,
    katilimci2_tc,
    katilimci2_dt,
    katilimci3_ad_soyad,
    katilimci3_tc,
    katilimci3_dt,
    katilimci4_ad_soyad,
    katilimci4_tc,
    katilimci4_dt,
    katilimci5_ad_soyad,
    katilimci5_tc,
    katilimci5_dt,
    para_birimi,
    odeme_yontemi,
    toplam_vergi,
    toplam_masraf,
    urun_fiyati,
    musteri_notu
  ) VALUES ?
  ON DUPLICATE KEY UPDATE
    status = VALUES(status),
    musteri_id = VALUES(musteri_id),
    musteri_adi = VALUES(musteri_adi),
    vergi_no = VALUES(vergi_no),
    telefon = VALUES(telefon),
    kamp_adi = VALUES(kamp_adi),
    tarih = VALUES(tarih),
    konaklama = VALUES(konaklama),
    paket = VALUES(paket),
    katilimci1_ad_soyad = VALUES(katilimci1_ad_soyad),
    katilimci1_tc = VALUES(katilimci1_tc),
    katilimci1_dt = VALUES(katilimci1_dt),
    katilimci2_ad_soyad = VALUES(katilimci2_ad_soyad),
    katilimci2_tc = VALUES(katilimci2_tc),
    katilimci2_dt = VALUES(katilimci2_dt),
    katilimci3_ad_soyad = VALUES(katilimci3_ad_soyad),
    katilimci3_tc = VALUES(katilimci3_tc),
    katilimci3_dt = VALUES(katilimci3_dt),
    katilimci4_ad_soyad = VALUES(katilimci4_ad_soyad),
    katilimci4_tc = VALUES(katilimci4_tc),
    katilimci4_dt = VALUES(katilimci4_dt),
    katilimci5_ad_soyad = VALUES(katilimci5_ad_soyad),
    katilimci5_tc = VALUES(katilimci5_tc),
    katilimci5_dt = VALUES(katilimci5_dt),
    para_birimi = VALUES(para_birimi),
    odeme_yontemi = VALUES(odeme_yontemi),
    toplam_vergi = VALUES(toplam_vergi),
    toplam_masraf = VALUES(toplam_masraf),
    urun_fiyati = VALUES(urun_fiyati),
    musteri_notu = VALUES(musteri_notu);
`;

  const values = salesData.flatMap((order) => {
    return order.line_items.map((item) => [
      order.id + "" + item?.product_id + "" + item?.id,
      order.id,
      order.status,
      order.customer_id,
      order?.billing.first_name + " " + order?.billing.last_name,
      order?.billing.vergi_no,
      order?.billing.phone,
      item?.name,
      item?.meta_data[0]?.display_value,
      item?.meta_data[1]?.display_value,
      item?.meta_data[2]?.display_value,
      order?.katilimcilar.katilimci1_ad_soyad,
      order?.katilimcilar.katilimci1_tc,
      order?.katilimcilar.katilimci1_dt,
      order?.katilimcilar.katilimci2_ad_soyad,
      order?.katilimcilar.katilimci2_tc,
      order?.katilimcilar.katilimci2_dt,
      order?.katilimcilar.katilimci3_ad_soyad,
      order?.katilimcilar.katilimci3_tc,
      order?.katilimcilar.katilimci3_dt,
      order?.katilimcilar.katilimci4_ad_soyad,
      order?.katilimcilar.katilimci4_tc,
      order?.katilimcilar.katilimci4_dt,
      order?.katilimcilar.katilimci5_ad_soyad,
      order?.katilimcilar.katilimci5_tc,
      order?.katilimcilar.katilimci5_dt,
      order?.currency_symbol,
      order?.payment_method,
      item?.total_tax,
      item?.cog_item_total_cost,
      item?.total,
      order?.customer_note,
    ]);
  });

  try {
    await pool.query(sql, [values.reverse()]);
    console.log(`Inserted ${values.length} sales records into the database`);
  } catch (error) {
    console.error("Error inserting data:", error);
  }
}

async function main() {
  try {
    const createTableSql = `
      CREATE TABLE IF NOT EXISTS sales (
        id BIGINT(20) UNSIGNED NOT NULL,
        siparis_id BIGINT(20) UNSIGNED ,
        status VARCHAR(255),
        musteri_id BIGINT(20) UNSIGNED,
        musteri_adi VARCHAR(255),
        vergi_no VARCHAR(50),
        telefon VARCHAR(50),
        kamp_adi VARCHAR(255),
        tarih VARCHAR(255),
        konaklama VARCHAR(255),
        paket VARCHAR(255),
        katilimci1_ad_soyad VARCHAR(255),
        katilimci1_tc VARCHAR(50),
        katilimci1_dt VARCHAR(50),
        katilimci2_ad_soyad VARCHAR(255),
        katilimci2_tc VARCHAR(50),
        katilimci2_dt VARCHAR(50),
        katilimci3_ad_soyad VARCHAR(255),
        katilimci3_tc VARCHAR(50),
        katilimci3_dt VARCHAR(50),
        katilimci4_ad_soyad VARCHAR(255),
        katilimci4_tc VARCHAR(50),
        katilimci4_dt VARCHAR(50),
        katilimci5_ad_soyad VARCHAR(255),
        katilimci5_tc VARCHAR(50),
        katilimci5_dt VARCHAR(50),
        para_birimi VARCHAR(50),
        odeme_yontemi VARCHAR(50),
        toplam_vergi DECIMAL(10,2),
        toplam_masraf DECIMAL(10,2),
        urun_fiyati DECIMAL(10,2),
        musteri_notu TEXT,
        PRIMARY KEY (id)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    `;

    const dropTableSql = `
        drop table sales,time;
      `;

    const createTimeTableSql = `
      CREATE TABLE time (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        run_time DATETIME NOT NULL
      );`;

    await new Promise((resolve, reject) => {
      pool.query(dropTableSql, (error, results, fields) => {
        if (error) {
          reject(error);
        } else {
          resolve(results);
        }
      });
    });

    await new Promise((resolve, reject) => {
      pool.query(createTimeTableSql, (error, results, fields) => {
        if (error) {
          reject(error);
        } else {
          resolve(results);
        }
      });
    });

    await new Promise((resolve, reject) => {
      pool.query(createTableSql, (error, results, fields) => {
        if (error) {
          reject(error);
        } else {
          resolve(results);
        }
      });
    });

    const salesData = await getSalesData();
    await insertSalesData(salesData);
    const currentTime = new Date();
    const threeHoursLater = new Date(currentTime.getTime() + 3 * 60 * 60 * 1000);
    const formattedTime = threeHoursLater.toISOString().slice(0, 19).replace("T", " ");
    console.log(`Inserted ${salesData.length} sales records into the database at ${formattedTime}`);
  } catch (error) {
    console.error(error);
  } finally {
    pool.end();
  }
}

// Scripti çalıştır ama cron ile değil
main();