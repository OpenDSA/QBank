from django.conf.urls import patterns, url, include
from django.views.generic import TemplateView

urlpatterns = patterns('',
	url(r'^$', 'qtool.views.index'),
	url(r'^problems/$', 'qtool.views.problems'),
	url(r'^(?P<problem_id>\d+)/details', 'qtool.views.details'),
	url(r'^success/', TemplateView.as_view(template_name = "qtool/success.html")),
	)


