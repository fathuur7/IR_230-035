import scrapy
import json

class IntroSpider(scrapy.Spider):
    name = "book_spider"

    def start_requests(self):
        base_url = 'http://books.toscrape.com/catalogue/page-{}.html'
        for page in range(1, 51):  # Ambil hingga halaman 50
            yield scrapy.Request(url=base_url.format(page), callback=self.parse)
            print('Scraping page', page)

    def parse(self, response):
        books = []
        for book in response.css('article.product_pod'):
            title = book.css('h3 > a::attr(title)').get()
            price = book.css('div.product_price > p.price_color::text').get()
            relative_image_url = book.css('div.image_container > a > img::attr(src)').get()
            relative_url = book.css('h3 > a::attr(href)').get()
            
            # Pastikan URL lengkap
            full_image_url = response.urljoin(relative_image_url)
            full_url = response.urljoin(relative_url)
            
            books.append({
                'book_title': title,
                'price': price,
                'image-url': full_image_url,
                'url': full_url
            })
            print(books)
        
        # Simpan dalam file JSON
        with open("books.json", "a", encoding="utf-8") as f:
            for book in books:
                json.dump(book, f)
                f.write("\n")
