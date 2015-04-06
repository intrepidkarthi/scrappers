from urllib2 import urlopen
from BeautifulSoup import BeautifulSoup
from HTMLParser import HTMLParser

def main():
    html_parser = HTMLParser()

    soup = BeautifulSoup(urlopen("http://www.amazon.com/gp/bestsellers/").read())

    categories = []

    # Scrape list of category names and urls
    for category_li in soup.find(attrs={'id':'zg_browseRoot'}).find('ul').findAll('li'):
        category = {}
        category['name'] = html_parser.unescape(category_li.a.string)
        category['url'] = category_li.a['href']

        categories.append(category)

    del soup

    # Loop through categories and print out each product's name, rank, and url.
    for category in categories:
        print category['name']
        print '-'*50

        soup = BeautifulSoup(urlopen(category['url']))

        i = 1
        for title_div in soup.findAll(attrs={'class':'zg_title'}):
            if i ==1:
                print "%d. %s\n    %s" % (i, html_parser.unescape(title_div.a.string), title_div.a['href'].strip())
            i += 1

        print ''

if __name__ == '__main__':
    main()