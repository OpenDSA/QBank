from django.conf.urls import patterns, url, include
from django.views.generic import TemplateView

urlpatterns = patterns('',
	url(r'^$', TemplateView.as_view(template_name = "qtool/splashpage.html")),
	url(r'^/$', TemplateView.as_view(template_name = "qtool/splashpage.html")),
	url(r'^about/$', TemplateView.as_view(template_name = "qtool/about.html")),
	url(r'^index/', TemplateView.as_view(template_name = "qtool/index.html")),
	url(r'^help/', TemplateView.as_view(template_name = "qtool/help.html")),
	url(r'^contact/', TemplateView.as_view(template_name = "qtool/contact.html")),

	url(r'^add/$',  'qtool.views.index'),
	url(r'^simple/$',  'qtool.views.simple'),	
	url(r'^list/$',  'qtool.views.list'),
	url(r'^range/$', 'qtool.views.range'),
	url(r'^summative/$', 'qtool.views.summative'),

	url(r'^edit/(?P<problem_id>\d+)/$',  'qtool.views.edit'),
	
	url(r'^delete/(?P<problem_id>\d+)/$',  'qtool.views.delete'),

	url(r'^problems/$', 'qtool.views.problems'),
	url(r'^export/$', 'qtool.views.export'),
	url(r'^problems_Summary/$', 'qtool.views.problems_Summary'),
	
	url(r'^(?P<problem_id>\d+)/ka_details', 'qtool.views.ka_details'),
	url(r'^edit_ka/(?P<problem_id>\d+)/$',  'qtool.views.edit_ka'),
	url(r'^(?P<problem_id>\d+)/ka_gen', 'qtool.views.ka_gen'),
	
	url(r'^(?P<problem_id>\d+)/simple_details', 'qtool.views.simple_details'),
	url(r'^edit_simple/(?P<problem_id>\d+)/$',  'qtool.views.edit_simple'),
	
	url(r'^(?P<problem_id>\d+)/list_details', 'qtool.views.list_details'),
	url(r'^edit_list/(?P<problem_id>\d+)/$',  'qtool.views.edit_list'),
	
	url(r'^(?P<problem_id>\d+)/range_details', 'qtool.views.range_details'),
	url(r'^edit_range/(?P<problem_id>\d+)/$',  'qtool.views.edit_range'),
	
	url(r'^(?P<problem_id>\d+)/summative_details', 'qtool.views.summative_details'),
	url(r'^edit_summative/(?P<problem_id>\d+)/$',  'qtool.views.edit_summative'),
	
	url(r'^(?P<problem_id>\d+)/write_file', 'qtool.views.write_file'),
	url(r'^(?P<problem_id>\d+)/d$',  'qtool.views.d'),
	url(r'^(?P<path>.*)$', 'django.views.static.serve',
	{'document_root': '/home/annp89/quiz/qtool/media/'}),
	)


